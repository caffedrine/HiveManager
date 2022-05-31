#ifndef HTTPWEBREQUEST_H_
#define HTTPWEBREQUEST_H_

#include <WString.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <WiFiClientSecure.h>


class HttpWebResponse
{
public:
	t_http_codes HttpCode;
	String responseString;
};

class HttpWebRequest
{
public:
	HttpWebRequest(String url);
	HttpWebResponse GET();

	String getProtocol() {return this->protocol;};
	String getHost() {return this->host;};
	String getPath() {return this->path;};
	int getPort() {return this->port;};

private:
	String protocol;
	String host;
	String path;
	int port;



};

#endif /* HTTPWEBREQUEST_H_ */
