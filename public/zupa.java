package Zuppa;
import ZAIP_SDK.ZuppaSDK;
import java.net.*;
import java.sql.*;
import java.util.LinkedHashMap;
import java.util.Map;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.UnsupportedEncodingException;

public class Test
{
	static class Coords
	{
		public int id;
		public int lat;
		public int lon;
		public short alt;
		
		public Coords()
		{}
		public Coords(int id, int lat, int lon, short alt)
		{
			this.id=id;
			this.lat=lat;
			this.lon=lon;
			this.alt=alt;
		}
	}
	static boolean hasWayPointRadiusBeenSet=false;
	public static final int WAYPOINT_RADIUS_DESIRED=3; // 5 meters
	static final String JDBC_DRIVER = "com.mysql.jdbc.Driver"; 
	static final String DB_URL = "jdbc:mysql://localhost/r2robotronics";
	static final String USER = "pi";
	static final String PASS = "p";
	static String server = "192.168.43.66";// "52.221.233.235"; // PASS AS ARGUMENT TO JAR, "java -jar test.jar 192.168.1.1"
	static String loc = "/cloud_nav_sys/software/public/drone.php?"; // HAVE TO CONFIRM THIS WITH PKB
	static int did = 35; //DRONE ID STATIC FOR NOW. not sure how to update it per drone. 
	static Coords[] ways; //Saves all waypoints
	static int rows = 0; //Number Of waypoints
	private static ZuppaSDK mySDK; 
	private static Connection conn = null; // global connection variable
	
	//private static float tHeight = 20.0f; // initial takeoff height in meters
	
	public static boolean reach(int latMaj, int lonMaj,short altM)
	{
		boolean uploadedProperly=false;
		
		mySDK.setZuppaModePositionHold();
		
		ZAIP_SDK.ZuppaInterface.Position p=mySDK.zaipInterface.getNewPosition();
			p.pointer=(byte) (mySDK.zaipInterface.WAYPOINT_LOCATION_PNTR);
			p.latitude=latMaj;
			p.longitude=lonMaj;
			p.altitude= altM;
			p.tt=(byte) 7;
			
		
		mySDK.zaipInterface.sendLocation(p);
			
		mySDK.zaipInterface.delay(200);
		mySDK.zaipInterface.generalParams.maxNumberOfWayPoints=1;
		
		mySDK.zaipInterface.commandToSetMaxWayPoint();
		
		mySDK.zaipInterface.delay(3000);
		
		mySDK.setZuppaModeNavigation();
		
		mySDK.zaipInterface.delay(3000);
		
		if((mySDK.zaipInterface.targetPosition.latitude==latMaj)&&(mySDK.zaipInterface.targetPosition.longitude==lonMaj))
		{
			uploadedProperly=true;
		}
		
		System.out.println("point : "+p.latitude+", "+p.longitude+", "+p.altitude);
		System.out.println("SET : "+mySDK.zaipInterface.targetPosition.latitude+", "+mySDK.zaipInterface.targetPosition.longitude+", "+mySDK.zaipInterface.targetPosition.altitude);
		
		return uploadedProperly;
	}
	
	private static void initialization()
	{
		System.out.println("Voltage On board : "+ mySDK.getCurrentVoltageOnBoard());
		System.out.println("Waiting For GPS Lock.");
		float HDOP;
		do
		{
			HDOP = mySDK.getHDOPValue();
			System.out.println("HDOP : "+HDOP);
			mySDK.zaipInterface.delay(1000);
		}while(HDOP <=0 || HDOP >5);

		System.out.println("GOT DESIRED HDOP");
		System.out.println("GpsNumSats = "+ mySDK.getNumberOfSats());
		mySDK.zaipInterface.delay(1000);
		System.out.println("Home Position : "+mySDK.zaipInterface.homePosition.latitude+", "+mySDK.zaipInterface.homePosition.longitude);
		
		int wpRad =mySDK.getCurrentWayPointRadius();
		
		if(wpRad != WAYPOINT_RADIUS_DESIRED)
		{
			
			System.out.println("THE ORIGINAL WAYPOINT RADIUS IS : "+mySDK.getCurrentWayPointRadius());
			while(wpRad != WAYPOINT_RADIUS_DESIRED)
			{
				mySDK.setCurrentWayPointRadius(WAYPOINT_RADIUS_DESIRED);
				mySDK.zaipInterface.delay(500);
				wpRad = mySDK.getCurrentWayPointRadius();
			}
			hasWayPointRadiusBeenSet=true;
			System.out.println("THE SET WP RADIUS IS : "+wpRad);
		}
		
		mySDK.setZuppaArmed();
		System.out.println("Zuppa ARMED");
	}
	
	private static void navigation()
	{
		//boolean emergencyCall = true; //false; // flag to trip emergency function.
		float dft; // dft = distance from target

		/*mySDK.setTargetAltitudeMeters(height); //takeoff height
		System.out.println("Height :"+height+"m sent!");
		while(!mySDK.checkIfAltitudeReached())
		{
			mySDK.zaipInterface.delay(1000);
			System.out.println(" f: "+mySDK.getCurrentFusedAltitude());
		}
		System.out.println("HEIGHT REACHED"); */

		System.out.println("STARTING NAVIGATION");
		int i=0;
		try
		{
			//Statement ecall = conn.createStatement(); // initialization of recall command.
			//String sql = "SELECT * FROM calls WHERE drone_id ="+did; // actual recall statement.
			while(i<rows) // till all rows used
			{
				System.out.println("Fetched : "+ways[i].id+" "+ways[i].lat+" "+ways[i].lon);
				mySDK.setZuppaModePositionHold();
				while(!reach(ways[i].lat, ways[i].lon, ways[i].alt))
				{
					System.out.println("Problem setting Waypoint.");
					System.out.println();
					mySDK.zaipInterface.delay(500);
				}
				long now, last = 0;
				dft = mySDK.zaipInterface.generalParams.distanceToTgt;
				do
				{
					now = System.currentTimeMillis();
					if(now - last >= 1500) // update only after 1 second.
					{
						dft = mySDK.zaipInterface.generalParams.distanceToTgt;
						last = now;
						updateServer();
						System.out.println("SET["+i+"] : "+mySDK.zaipInterface.targetPosition.latitude+", "+mySDK.zaipInterface.targetPosition.longitude+", "+mySDK.zaipInterface.targetPosition.altitude);
						/*if(!emergencyCall) // query DB every 1 sec while flying ?
						{
							ResultSet ec = ecall.executeQuery(sql);
							ec.last();
							if( ec.getRow()!=0 ) // to check if row exists
							{
								ec.first();
								if(ec.getInt("ecall") != 0) // to check if recalled or not
								{
									System.out.println("EMERGANCY CALL!");
									emergencyCall = true;
									sql = "SELECT * FROM ways ORDER BY id DESC WHERE id < "+ways[i].id;
									ResultSet ec2 = ecall.executeQuery(sql); // here, updating the original waypoint's cursor, rs.
									i=0;
									while(ec2.next()) // till all rows used
									{
										ways[i].id = ec2.getInt("id");
										ways[i].lat = ec2.getInt("way_lati");
										ways[i].lon = ec2.getInt("way_long");
										ways[i].alt = ec2.getShort("altitude");
										i++;
									}
									rows = i;
									i=0;
									System.out.println("returning to:-");
									System.out.println("la "+ways[i].lat+" lo "+ways[i].lon);
									int wdt = 5; // IF return WP is too close to Current position, it will not update. try "wdt" times, then upload next waypoint
									while(!reach(ways[i].lat, ways[i].lon, ways[i].alt))
									{
										System.out.println("Return WP maybe too Close");
										--wdt;
										System.out.println();
										mySDK.zaipInterface.delay(500);
										if(wdt == 0)
										{
											i++;
										}
									}
								}
							}
						}*/
						System.out.println("DFT : "+dft);
					}
				}while(dft > 5.0); // wait until reached waypoint then upload next waypoint.
				i++;
			}
		}
		//catch (SQLException e){e.printStackTrace();}
		finally {}
	}
	
	private static void updateServer()
	{
		System.out.println("POSTING");
		URL url;
		try {url = new URL("http://"+server+loc);}
		catch (MalformedURLException e)
		{
			e.printStackTrace();
			System.out.println("URL FAIL : Skipping Post.");
			return;
		}
		float clat = (float)mySDK.zaipInterface.currentPosition.latitude/10000000.00f;
		float clon = (float)mySDK.zaipInterface.currentPosition.longitude/10000000.00f;
		float calt = (float)mySDK.getCurrentFusedAltitude(); // in Meters AGL
		Map<String,Object> params = new LinkedHashMap<>();
		params.put("drone_id", did);
		params.put("altitude", calt);
		params.put("latitude", clat);
		params.put("longitude", clon);
		params.put("head", mySDK.getCurrentHeading());
		params.put("pitch", -1.0f*mySDK.getYAngle());
		params.put("roll", mySDK.getXAngle());
		params.put("batt", mySDK.getCurrentVoltageOnBoard());
		params.put("airspeed", (mySDK.getSpeedYDirectionCmPerSec())/100.0f);
		params.put("vert_speed", mySDK.getVerticalRateCmPerSec());
		params.put("hub_id", "ndls");
		params.put("flight", 1); //static ???
		params.put("power", 1); // static ???
		params.put("bank", mySDK.getXAxisGyroDegPerSec());
		StringBuilder postData = new StringBuilder();
		for (Map.Entry<String,Object> param : params.entrySet())
		{
			if (postData.length() != 0) postData.append('&');
			try
			{
				postData.append(URLEncoder.encode(param.getKey(), "UTF-8"));
				postData.append('=');
				postData.append(URLEncoder.encode(String.valueOf(param.getValue()), "UTF-8"));
			}
			catch (UnsupportedEncodingException e)
			{
				e.printStackTrace();
				System.out.println("Encoding failed for keyValue");
				continue;
			}
		}
		String urlParameters = postData.toString();
		try
		{
			URLConnection http = url.openConnection();
			http.setDoOutput(true);
			OutputStreamWriter writer = new OutputStreamWriter(http.getOutputStream());
			writer.write(urlParameters);
			writer.flush();
			String result = "";
			String line;
			BufferedReader reader = new BufferedReader(new InputStreamReader(http.getInputStream()));
			while ((line = reader.readLine()) != null)
			{
				result += line;
			}
			writer.close();
			reader.close();
			System.out.println("Current: "+clat+", "+clon+", "+calt);
			System.out.println("Server Says : "+result);
		}
		catch (IOException e) {e.printStackTrace();}
	}
	
	public static void main(String[] args)
	{
		//System.load("/home/pi/ZuppaWS/ZuppaTest/src/ref/linux-armv6hf/libjSSC-2.8.so");
		//System.load("/home/pi/ZuppaWS/integration/src/ref/linux-armv6hf/libjSSC-2.8.so");
		try
		{
			server = args[0]; //take server as input from commandline arguments
		}
		catch( ArrayIndexOutOfBoundsException e )
		{
			System.out.println("using hardcoded Server");
		}
		finally
		{
			System.out.println("SERVER SET : "+server);
		}
		/*Statement stmt = null;
		try
		{
			Class.forName("com.mysql.jdbc.Driver");
			System.out.println("Connecting to database...");
			conn = DriverManager.getConnection(DB_URL, USER, PASS);
			System.out.println("Database connected successfully...");
			rows = 0;
			stmt = conn.createStatement();
			String sql = "SELECT * FROM ways";
			ResultSet rs = null;
			while(rows == 0)
			{
				rs = stmt.executeQuery(sql);
				rs.last();
				rows = rs.getRow();
				System.out.println("Waiting for ways.");
				mySDK.zaipInterface.delay(1000);
			}
			rs.beforeFirst();
			System.out.println("GOT WAYS");
			ways = new Coords[rows];
			int i = 0;
			while(rs.next()) // till all rows used
			{
				ways[i] = new Coords();
				ways[i].id = rs.getInt("id");
				ways[i].lat = rs.getInt("way_lati");
				ways[i].lon = rs.getInt("way_long");
				ways[i].alt = rs.getShort("altitude");
				i++;
			}
		}
		catch(SQLException se){se.printStackTrace();}
		catch (ClassNotFoundException e) {e.printStackTrace();}
		catch(Exception e){e.printStackTrace();}
		finally
		{
			try{if(stmt!=null)stmt.close();}
			catch(SQLException se2){}
		}*/
		
		ways = new Coords[4];
		ways[0] = new Coords(101, 128441354, 801523956, (short) 100);
		ways[1] = new Coords(102, 128445145, 801520805, (short) 100);
		ways[2] = new Coords(103, 128448964, 801524157, (short) 100);
		ways[3] = new Coords(104, 128451265, 801526974, (short) 100);
		
		rows = 4;
		
		String COM_NAME = "/dev/ttyUSB0";
		mySDK = new ZuppaSDK(false); // false --> to disable UI
		mySDK.zaipInterface.port.connectPort(9600, COM_NAME);

		while(!mySDK.isConnectedToPort());
		while(!mySDK.isSDKConnectedToAutopilot());
		System.out.println("ZUPPA CONNECTED");

		initialization();
		updateServer();
		navigation();
		System.out.println("WAYPOINTS OVER");
		mySDK.zaipInterface.port.disconnectPort();
		try{if(conn!=null)conn.close();}
		catch(SQLException se){se.printStackTrace();}
	}
}