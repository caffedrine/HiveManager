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

    GroupBox {
        id: groupBox
        x: 12
        y: 11
        width: 379
        height: 198
        title: qsTr("Server configuration")

        TextField {
            id: textField
            x: 10
            y: 33
            width: 140
            height: 20
            text: "0.0.0.0"
            placeholderText: qsTr("0.0.0.0")
        }

        TextField {
            id: textField1
            x: 10
            y: 81
            width: 140
            height: 20
            text: "20000"
            placeholderText: qsTr("20000")
        }

        Text {
            id: text1
            x: 10
            y: 17
            text: qsTr("IP Address")
            font.pixelSize: 12
        }

        Text {
            id: text2
            x: 10
            y: 59
            text: qsTr("Port")
            font.pixelSize: 12
        }

        Button {
            id: button1
            x: 84
            y: 111
            text: qsTr("Stop server")
        }

        Button {
            id: button
            x: 10
            y: 111
            text: qsTr("Start server")
        }
    }


}
