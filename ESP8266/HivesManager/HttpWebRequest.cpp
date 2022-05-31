#include "HttpWebRequest.h"

HttpWebRequest::HttpWebRequest(String url)
{
	String tmpUrl = url;

	// Parse url
	if( tmpUrl.startsWith("https://") )
	{
		this->protocol = "https://";
		tmpUrl.replace("https://", "");
		this->port = 443;
	}
	else
	{
		this->protocol = "http://";
		tmpUrl.replace("http://", "");
		this->port = 80;
	}

	// Parse host and path
	if( tmpUrl.indexOf("/") )
	{
		this->host = tmpUrl.substring(0, tmpUrl.indexOf("/"));
		this->path = tmpUrl.substring(tmpUrl.indexOf("/"));
	}
	else
	{
		this->host = tmpUrl;
		this->path = "/";
	}
}


HttpWebResponse HttpWebRequest::GET()
{
	HTTPClient http;
	WiFiClientSecure client_secured;
	WiFiClient client_unsecured;


	if(protocol == "https://" )
	{
		client_secured.setInsecure();
		client_secured.connect(this->host, this->port);
		http.begin(client_secured, this->protocol + this->host + this->path);
	}
	else
	{
		http.begin(client_unsecured, this->host, this->port, this->path, false);
	}

	http.setFollowRedirects(HTTPC_FORCE_FOLLOW_REDIRECTS);
	http.setUserAgent("Hive harvester / esp8266");

	// Perform HTTP request
	HttpWebResponse response;
	response.HttpCode = (t_http_codes)http.GET();
	response.responseString = http.getString().substring(0, 1024);

	http.end();

	return response;

}

