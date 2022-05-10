
#ifndef _HIVESCONNECTIONSMANAGER_H_
#define _HIVESCONNECTIONSMANAGER_H_

#include <QObject>
#include <QTimer>
#include <QHostAddress>
#include "services/Tcp/TcpServerM.h"
#include "services/Http/HttpWebRequest.h"

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
    HttpWebRequest cloudHttp;

    void SendDataToCloud(const QString &client, const QByteArray &data);

private slots:
    void TcpClientConnected(QTcpSocket *client);
    void TcpClientDisconnected(QTcpSocket *client);
    void TcpClientDataReception(QTcpSocket *client);

    void Http_RequestStarted(const QString &requestMethod, const QNetworkRequest *request, const QByteArray &requestBody) const;
    void Http_RequestFinished(const HttpWebRequestsResponse *response) const;
    void Http_RequestReturnedError(const HttpWebRequestsResponse *response) const;

signals:
    void ActiveConnectionsCountChanged(quint32 count);
    void OnDataPacketAvailable(const QString &client, const QByteArray &data);

};

#endif // _HIVESCONNECTIONSMANAGER_H_