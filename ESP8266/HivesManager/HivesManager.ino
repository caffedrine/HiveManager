#include "Arduino.h"
#include <base64.h>
#include <ESP8266WiFi.h>

#include "UrlEncode.h"
#include "HttpWebRequest.h"

// ID Senzor
String SERIAL_NUMBER = "C000000001";

// WiFi router username/password
const char *ssid = "2G";
const char *password = "internatcamin";

struct SenzorStup
{
	int volts_mv;
	int weight_gm;
};

struct SenzorAmbiental
{
	int volts_mv;
	float temperature_deg;
	int himidity_rh;
};

SenzorStup valoriStup;
SenzorAmbiental valoriAmbientale;

void setup_wifi()
{
	  Serial.print("Connecting to WiFi network " + String(ssid) + ":" + String(password));
	  WiFi.begin(ssid, password);
	  while (WiFi.status() != WL_CONNECTED)
	  {
	    delay(500);
	    Serial.print(".");
	  }
	  Serial.println("\nSuccessfully connected to " +  String(ssid) + ":" + String(password) + ", IP Address " + WiFi.localIP().toString() + ", gateway " + WiFi.gatewayIP().toString());
}

void SendDataToCloud()
{
	String data;
	if( SERIAL_NUMBER.charAt(0) == 'C' )
	{
		// Date senzor ambiental
		data = "volts_mv=" + String(valoriAmbientale.volts_mv);
		data += "&temperature_deg=" + String(valoriAmbientale.temperature_deg);
		data += "&humidity_rh=" + String(valoriAmbientale.himidity_rh);
	}
	else if( SERIAL_NUMBER.charAt(0) == 'S' )
	{
		// Date senzor stup
		data = "volts_mv=" +  String(valoriStup.volts_mv);
		data += "&weight_gm=" +  String(valoriStup.weight_gm);
	}

	// Check WiFi connection state
	if (WiFi.status() != WL_CONNECTED)
	{
		int WaitSeconds = 0;
		WiFi.begin(ssid, password);
		while (WiFi.status() != WL_CONNECTED)
		{
			delay(500);
			Serial.print(".");
			if( ++WaitSeconds >= 40  ) // 20 seconds timeout
				return;
		}
	}

	// Send to cloud
	String url = "https://stupar.254.ro/api/v1/add?sensor_id=" + SERIAL_NUMBER + "&data=" + base64::encode(data) + "&signature=dummy";
	//Serial.println("GET " + url);

	// Send HTTP request and print response
	HttpWebRequest http(url);
	HttpWebResponse response = http.GET();
	Serial.println(String(response.HttpCode) + " GET " + http.getProtocol() + http.getHost() + ":" + String(http.getPort()) + http.getPath() + "\n" + response.responseString);
}

void setup()
{
	Serial.begin(9600);
	Serial.println("Application started...");

	// Connect to WiFi AP
	setup_wifi();

	// Valori ambientale
	valoriAmbientale.volts_mv = 5000;
	valoriAmbientale.temperature_deg = 25;
	valoriAmbientale.himidity_rh = 63;

	// Valori stup
	valoriStup.volts_mv = 5000;
	valoriStup.weight_gm = 1500;
}


uint64 prevMillis = 0;
void loop()
{
	if( millis() < prevMillis ) // Check for counter overflow
	{
		prevMillis = 0;
	}
	if( millis() - prevMillis >= 1000 * 60 * 10 ) 	// Send data to cloud every 10 minutes
	{
		prevMillis = millis();

		// Trimite valori ambientale in cloud
		valoriAmbientale.volts_mv -= random(1, 3);
		if (valoriAmbientale.volts_mv <= 0)
			valoriAmbientale.volts_mv = 5000;
		valoriAmbientale.temperature_deg -= (float)random(1, 5) / 10.0;
		if (valoriAmbientale.temperature_deg <= 5)
			valoriAmbientale.temperature_deg = 30;
		valoriAmbientale.himidity_rh = random(35, 80);
		SERIAL_NUMBER = "C000000001";
		SendDataToCloud();

		// Trimite valori stup in cloud
		valoriStup.volts_mv -= random(1, 3);
		if (valoriStup.volts_mv <= 0)
			valoriStup.volts_mv = 5000;
		valoriStup.weight_gm += random(1, 5);
		if (valoriStup.weight_gm >= 7000)
			valoriStup.weight_gm = 1500;
		SERIAL_NUMBER = "S000000001";
		SendDataToCloud();
	}
}
