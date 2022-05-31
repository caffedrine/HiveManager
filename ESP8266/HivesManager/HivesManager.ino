#include "Arduino.h"
#include <base64.h>
#include <ESP8266WiFi.h>

#include "HttpWebRequest.h"

// WiFi router username/password
const char *ssid = "2G_Secured";
const char *password = "internatcamin";

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
	String stup_id = "stup_01";
	String data = "masa=1250, umiditate=1374, temperatura=12";

	// Send to cloud
	String url = "https://stupar.254.ro/add.php?stup_id=" + stup_id + "&data=" + base64::encode(data);
	Serial.println("GET " + url);

	// Send HTTP request and print response
	HttpWebRequest http(url);
	HttpWebResponse response = http.GET();
	Serial.println(String(response.HttpCode) + " " + http.getProtocol() + http.getHost() + ":" + String(http.getPort()) + http.getPath() + "\n" + response.responseString);
}

void setup()
{
	Serial.begin(9600);
	Serial.println("Application started...");

	// Connect to WiFi AP
	setup_wifi();

	SendDataToCloud();
}

void loop()
{

}
