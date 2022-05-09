#ifndef _TCPSERVERM_H_
#define _TCPSERVERM_H_

#include <QObject>
#include <QHostAddress>
#include <QTcpServer>
#include <QTcpSocket>

class TcpServerM: public QObject
{
Q_OBJECT
public:
    TcpServerM();
    ~TcpServerM() override;

    bool StartListening(const QHostAddress &addr, quint16 port);
    void StopListening();

    qint64 WriteData(qintptr clientSockFd, const QByteArray &data);

    void SetMaxConnections(quint16 max_connections);
    quint32 GetConnectionsCount();

protected:

private:
    quint16 max_clients = 1000;
    QTcpServer server;
    QMap<qintptr, QTcpSocket *> clients;


private slots:
    void newConnection();

signals:
    void TcpClientConnected(QTcpSocket *client);
    void TcpClientDisconnected(QTcpSocket *client);
    void TcpClientDataReception(QTcpSocket *client);


};

#endif // _TCPSERVERM_H_