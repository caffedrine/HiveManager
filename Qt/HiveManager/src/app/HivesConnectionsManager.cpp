#include "HivesConnectionsManager.h"

HivesConnectionsManager::HivesConnectionsManager(): QObject()
{
    QObject::connect(&this->tcpServer, SIGNAL(TcpClientConnected(QTcpSocket *)), this, SLOT(TcpClientConnected(QTcpSocket *)));
    QObject::connect(&this->tcpServer, SIGNAL(TcpClientDisconnected(QTcpSocket *)), this, SLOT(TcpClientDisconnected(QTcpSocket *)));
    QObject::connect(&this->tcpServer, SIGNAL(TcpClientDataReception(QTcpSocket *)), this, SLOT(TcpClientDataReception(QTcpSocket *)));

    this->cloudHttp.setIgnoreSslErrors(true);
    QObject::connect(&this->cloudHttp, SIGNAL(RequestStarted(const QString &, const QNetworkRequest *, const QByteArray &)), this, SLOT(Http_RequestStarted(const QString &, const QNetworkRequest *, const QByteArray &)));
    QObject::connect(&this->cloudHttp, SIGNAL(RequestFinished(const HttpWebRequestsResponse *)), this, SLOT(Http_RequestFinished(const HttpWebRequestsResponse *)));
    QObject::connect(&this->cloudHttp, SIGNAL(RequestReturnedError(const HttpWebRequestsResponse *)), this, SLOT(Http_RequestReturnedError(const HttpWebRequestsResponse *)));
}

HivesConnectionsManager::~HivesConnectionsManager()
{

}

bool HivesConnectionsManager::StartListen(const QHostAddress &address, quint16 port)
{
    return this->tcpServer.StartListening(address, port);
}

void HivesConnectionsManager::StopListen()
{
    this->tcpServer.StopListening();
}

void HivesConnectionsManager::TcpClientConnected(QTcpSocket *client)
{
    qInfo().noquote().nospace() << "[TCP SERVER] Client " << client->localAddress() << " connected";
    emit this->ActiveConnectionsCountChanged(this->tcpServer.GetConnectionsCount());
}

void HivesConnectionsManager::TcpClientDisconnected(QTcpSocket *client)
{
    qInfo().noquote().nospace() << "[TCP SERVER] Client " << client->localAddress() << " disconnected";
    emit this->ActiveConnectionsCountChanged(this->tcpServer.GetConnectionsCount());
}

void HivesConnectionsManager::TcpClientDataReception(QTcpSocket *client)
{
    qInfo().noquote().nospace() << "[TCP SERVER] Available to read " << client->bytesAvailable() << " bytes from client " << client->peerAddress();
    QByteArray data = client->readAll();
    QString hiveAddress = client->peerAddress().toString();

    emit this->OnDataPacketAvailable(  hiveAddress, data );

    // Send data to webserver
    this->SendDataToCloud(hiveAddress, data);
}

void HivesConnectionsManager::SendDataToCloud(const QString &client, const QByteArray &data)
{
    QString url = "https://stupar.254.ro/add";
    url += "?client=" + client;
    url += "&data=" + QString(data.toBase64());
    this->cloudHttp.GET(url);
}

void HivesConnectionsManager::Http_RequestStarted(const QString &requestMethod, const QNetworkRequest *request, const QByteArray &requestBody) const
{
    qInfo().nospace().noquote() << "[CLOUD HTTP REQUEST - START] " << requestMethod << " " << request->url();
}

void HivesConnectionsManager::Http_RequestReturnedError(const HttpWebRequestsResponse *response) const
{
    qWarning().nospace().noquote() << "[CLOUD HTTP REQUEST - ERROR] " << response->ErrorString();
}

void HivesConnectionsManager::Http_RequestFinished(const HttpWebRequestsResponse *response) const
{
    qInfo().nospace().noquote() << "[CLOUD HTTP REQUEST - FINISH] " << response->HttpCode << " " << response->reply->url();
}