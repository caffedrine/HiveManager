
#ifndef _HIVESCONNECTIONSMANAGER_H_
#define _HIVESCONNECTIONSMANAGER_H_

#include <QObject>
#include <QTimer>
#include <QHostAddress>
#include "TcpServerM/TcpServerM.h"

class HivesConnectionsManager: public QObject
{
Q_OBJECT
public:
    HivesConnectionsManager();
    ~HivesConnectionsManager() override;

    bool StartListen(const QHostAddress &address, quint16 port);
    void StopListen();

protected:

private:
    TcpServerM tcpServer;

private slots:
    void TcpClientConnected(QTcpSocket *client);
    void TcpClientDisconnected(QTcpSocket *client);
    void TcpClientDataReception(QTcpSocket *client);

signals:
    void ActiveConnectionsCountChanged(quint32 count);
    void OnDataPacketAvailable(const QHostAddress &client, const QByteArray &data);

};

#endif // _HIVESCONNECTIONSMANAGER_H_