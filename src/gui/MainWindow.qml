import QtQuick 2.15
import QtQuick.Window 2.15
import QtQuick.Controls 2.15
import QtQuick.Layouts 1.15

Window
{
    id: root
    visible: true
    width: 400
    height: 400
    title: qsTr("Hive Manager")

    ColumnLayout
    {
        id: column_server_config
        anchors.top: size_fields.bottom
        anchors.topMargin: 5
        anchors.horizontalCenter: parent.horizontalCenter
        spacing: 20

        GroupBox
        {
            id: groupBox
            x: 12
            width: 379
            height: 166
            anchors.top: parent.top
            anchors.topMargin: 11
            title: qsTr("Server configuration")

            Text {
                id: text1
                x: 10
                y: 17
                text: qsTr("IP Address")
                font.pixelSize: 12
            }

            TextField {
                id: textField_serverIP
                x: 10
                y: 33
                width: 359
                height: 20
                text: "0.0.0.0"
                placeholderText: qsTr("0.0.0.0")
            }

            Text {
                id: text2
                x: 10
                y: 59
                text: qsTr("Port")
                font.pixelSize: 12
            }

            TextField {
                id: textField_ServerPort
                x: 10
                y: 81
                width: 359
                height: 20
                text: "20000"
                placeholderText: qsTr("20000")

            }

            Button {
                id: button_StopServer
                x: 198
                y: 129
                width: 171
                height: 24
                text: qsTr("Stop server")
            }

            Button {
                id: button_StartServer
                x: 10
                y: 129
                width: 171
                height: 24
                text: qsTr("Start server")
            }

            Text {
                id: text_ServerState
                x: 10
                y: 107
                text: qsTr("Server state: STOPPED")
                font.pixelSize: 12
            }
        }
    }

    GroupBox {
        id: groupBox1
        x: 12
        width: 379
        height: 204
        anchors.top: parent.top
        anchors.topMargin: 183
        title: qsTr("Logs")

        Button {
            id: button_ClearLogs
            x: 10
            y: 170
            width: 359
            height: 24
            text: qsTr("Clear logs")
        }

        TextArea {
            id: textArea_Logs
            x: 9
            y: 22
            width: 360
            height: 142
            placeholderText: qsTr("Text Area")
        }
    }


}
