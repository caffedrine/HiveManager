#include "tcpconnection.h"

TcpConnection::TcpConnection(QObject *parent) : QObject(parent)
{
    qDebug() << this << "Created";
}

TcpConnection::~TcpConnection()
{
    qDebug() << this << "Destroyed";
}

void TcpConnection::setSocket(QTcpSocket *socket)
{
    m_socket = socket;
    connect(m_socket,&QTcpSocket::connected, this, &TcpConnection::connected);
    connect(m_socket,&QTcpSocket::disconnected, this, &TcpConnection::disconnected);
    connect(m_socket,&QTcpSocket::readyRead, this, &TcpConnection::readyRead);
    connect(m_socket,&QTcpSocket::bytesWritten, this, &TcpConnection::bytesWritten);
    connect(m_socket,&QTcpSocket::stateChanged, this, &TcpConnection::stateChanged);
    //connect(m_socket,&QTcpSocket::error,this, error(QAbstractSocket::SocketError));

}

QTcpSocket *TcpConnection::getSocket()
{
    if(!sender()) return 0;
    return static_cast<QTcpSocket*>(sender());
}

void TcpConnection::connected()
{
    if(!sender()) return;
    qDebug() << this << " connected " << sender();
}

void TcpConnection::disconnected()
{
    if(!sender()) return;
    qDebug() << this << " disconnected " << getSocket();
}

void TcpConnection::readyRead()
{
    if(!sender()) return;
    qDebug() << this << " readyRead " << getSocket();
    QTcpSocket *socket = getSocket();
    if(!socket) return;
    socket->close();
}

void TcpConnection::bytesWritten(qint64 bytes)
{
    if(!sender()) return;
    qDebug() << this << " bytesWritten " << getSocket() << " number of bytes = " << bytes;
}

void TcpConnection::stateChanged(QAbstractSocket::SocketState socketState)
{
    if(!sender()) return;
    qDebug() << this << " stateChanged " << getSocket() << " state = " << socketState;
}

void TcpConnection::error(QAbstractSocket::SocketError socketError)
{
    if(!sender()) return;
    qDebug() << this << " error " << getSocket() << " error = " << socketError;
}

