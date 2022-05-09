#include "MainWindow.h"
#include "./ui_MainWindow.h"

MainWindow::MainWindow(QWidget *parent): QMainWindow(parent), ui(new Ui::MainWindow)
{
    ui->setupUi(this);
    this->ui->pushButton_StartServer->setDisabled(false);
    this->ui->pushButton_StopServer->setDisabled(true);

    this->hivesManager = new HivesConnectionsManager();
    connect(hivesManager, SIGNAL(ActiveConnectionsCountChanged(quint32)), this, SLOT(TcpConnections_OnCountChanged(quint32)));
    connect(hivesManager, SIGNAL(OnDataPacketAvailable(QHostAddress&,QByteArray&)), this, SLOT(TcpConnections_OnDataAvailable(QHostAddress&,QByteArray&)));
}

MainWindow::~MainWindow()
{
    delete ui;
}


void MainWindow::on_pushButton_StartServer_clicked()
{
    //this->hivesManager.StartListen(QHostAddress(this->ui->lineEdit_IpAddress->text()), this->ui->lineEdit_Port->text().toInt());
    this->ui->label_ServerStatus->setText("Server status: STARTED");
    this->ui->pushButton_StartServer->setDisabled(true);
    this->ui->pushButton_StopServer->setDisabled(false);
    qDebug().noquote().nospace() << "TCP Server STARTED on " + this->ui->lineEdit_IpAddress->text() + ":" + this->ui->lineEdit_Port->text();
}


void MainWindow::on_pushButton_StopServer_clicked()
{
    //this->hivesManager.StopListen();

    this->ui->label_ServerStatus->setText("Server status: STOPPED");
    this->ui->pushButton_StartServer->setDisabled(false);
    this->ui->pushButton_StopServer->setDisabled(true);
    qDebug().noquote().nospace() << "TCP Server STOPPED";
}


void MainWindow::on_pushButton_SaveSettings_clicked()
{

}


void MainWindow::on_pushButton_ClearLogs_clicked()
{

}

void MainWindow::TcpConnections_OnCountChanged(quint32 count)
{
    this->ui->label_ServerStatus->setText("Server status: STARTED, clients connected " + QString::number(count));
}

void MainWindow::TcpConnections_OnDataAvailable(const QHostAddress& clientSource, const QByteArray& data)
{
    this->ui->plainTextEdit_Logs->appendPlainText("[" + clientSource.toString() + "] " + data);
}

