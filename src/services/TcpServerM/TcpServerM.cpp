#include "TcpServerM.h"

TcpServerM::TcpServerM()
{
    QObject::connect(&this->server, SIGNAL(newConnection()), this, SLOT(newConnection()));
}

TcpServerM::~TcpServerM()
{

}

quint32 TcpServerM::GetConnectionsCount()
{
    return this->clients.count();
}

bool TcpServerM::StartListening(const QHostAddress &addr, quint16 port)
{
    return this->server.listen(QHostAddress::Any, port);
}

void TcpServerM::StopListening()
{
    for(QTcpSocket *socket: this->clients)
    {
        socket->close();
    }
    this->clients.clear();

    this->server.close();
}

void TcpServerM::newConnection()
{
    if(this->clients.count() < this->max_clients)
    {
        QTcpSocket *client = this->server.nextPendingConnection();
        this->clients[client->socketDescriptor()] = client;

        qintptr clientSockFd = client->socketDescriptor();
        QHostAddress clientAddr = client->peerAddress();

        QObject::connect(client, &QTcpSocket::disconnected,this, [this, client, clientSockFd, clientAddr]()
        {
            this->clients.remove(clientSockFd);
            emit this->TcpClientDisconnected(client);
        });

        QObject::connect(client, &QTcpSocket::readyRead,this, [this, client]()
        {
            emit this->TcpClientDataReception(client);
        });

        emit this->TcpClientConnected(client);
    }
    else
    {
        this->server.nextPendingConnection()->close();
    }
}

void TcpServerM::SetMaxConnections(quint16 max_connections)
{
    this->max_clients = max_connections;
}

qint64 TcpServerM::WriteData(qintptr clientSockFd, const QByteArray &data)
{
    if( !this->clients.contains(clientSockFd) )
    {
        qDebug().nospace().noquote() << "Invalid client FD requested: " << clientSockFd;
        return -1;
    }

    return this->clients[clientSockFd]->write(data);
}

