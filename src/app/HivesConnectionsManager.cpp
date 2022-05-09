#include "HivesConnectionsManager.h"

HivesConnectionsManager::HivesConnectionsManager(): QObject()
{
    QObject::connect(&this->tcpServer, SIGNAL(TcpClientConnected(QTcpSocket *)), this, SLOT(TcpClientConnected(QTcpSocket *)));
    QObject::connect(&this->tcpServer, SIGNAL(TcpClientDisconnected(QTcpSocket *)), this, SLOT(TcpClientDisconnected(QTcpSocket *)));
    QObject::connect(&this->tcpServer, SIGNAL(TcpClientDataReception(QTcpSocket *)), this, SLOT(TcpClientDataReception(QTcpSocket *)));
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
    emit this->ActiveConnectionsCountChanged(this->tcpServer.GetConnectionsCount());
}

void HivesConnectionsManager::TcpClientDisconnected(QTcpSocket *client)
{
    //qDebug() << "[HIVE] client disconnected";


    emit this->ActiveConnectionsCountChanged(this->tcpServer.GetConnectionsCount());
}

void HivesConnectionsManager::TcpClientDataReception(QTcpSocket *client)
{

    //qDebug() << "[HIVE] Data reception available";

    emit this->OnDataPacketAvailable(  client->peerAddress(), client->readAll() );
}
