#ifndef MAINWINDOW_H
#define MAINWINDOW_H

#include <QMainWindow>
#include <QDebug>
#include <QTimer>

#include "app/HivesConnectionsManager.h"

QT_BEGIN_NAMESPACE
namespace Ui { class MainWindow; }
QT_END_NAMESPACE

class MainWindow : public QMainWindow
{
        Q_OBJECT
    public:
        MainWindow(QWidget *parent = nullptr);
        ~MainWindow();

    private slots:
        void on_pushButton_StartServer_clicked();
        void on_pushButton_StopServer_clicked();
        void on_pushButton_SaveSettings_clicked();
        void on_pushButton_ClearLogs_clicked();
        void TcpConnections_OnCountChanged(quint32 count);
        void TcpConnections_OnDataAvailable(const QHostAddress& client, const QByteArray& data);

    private:
        Ui::MainWindow *ui;
        HivesConnectionsManager *hivesManager;

};
#endif // MAINWINDOW_H
