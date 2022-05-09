
#ifndef _HIVESCONNECTIONSMANAGER_H_
#define _HIVESCONNECTIONSMANAGER_H_

#include <QObject>
#include <QTimer>
#include <QHostAddress>
#include "TcpServerM/TcpServerM.h"

class HivesConnectionsManager: public QObject
{
public:
    HivesConnectionsManager();
    ~HivesConnectionsManager() override;

    void StartListen(const QHostAddress &address, quint16 port);
    void StopListen();

protected:

private:
    QTimer poolingTimer;

    signals:
    void ActiveConnectionsCountChanged(quint32 count);
    void OnDataPacketAvailable(const QHostAddress &client,const QByteArray &data);

signals:



};

#endif // _HIVESCONNECTIONSMANAGER_H_