cmake_minimum_required(VERSION 3.5)

project(HiveManager VERSION 0.1 LANGUAGES CXX)

set(CMAKE_INCLUDE_CURRENT_DIR ON)

set(CMAKE_AUTOUIC ON)
set(CMAKE_AUTOMOC ON)
set(CMAKE_AUTORCC ON)

set(CMAKE_CXX_STANDARD 17)
set(CMAKE_CXX_STANDARD_REQUIRED ON)

set(QT_ASSUME_STDERR_HAS_CONSOLE=1)

find_package(QT NAMES Qt6 Qt5)
find_package(Qt${QT_VERSION_MAJOR} REQUIRED COMPONENTS Core Widgets Network)

# Thirdparty libs
include("thirdparty/thirdparty_dependencies.cmake")

# Include SRC directory
include_directories("src")
include_directories("src/services")

set(PROJECT_SOURCES
        src/main.cpp
        src/app/HivesConnectionsManager.cpp
        src/core/Logger.cpp
        src/services/TcpServerM/TcpServerM.cpp
        src/gui/MainWindow.cpp
        src/gui/MainWindow.h
        src/gui/MainWindow.ui
        ${THIRD_PARTY_SRC}
)

if(${QT_VERSION_MAJOR} GREATER_EQUAL 6)
    qt_add_executable(HiveManager
        MANUAL_FINALIZATION
        ${PROJECT_SOURCES}
        ${THIRD_PARTY_SRC}
    )
# Define target properties for Android with Qt 6 as:
#    set_property(TARGET HiveManager APPEND PROPERTY QT_ANDROID_PACKAGE_SOURCE_DIR
#                 ${CMAKE_CURRENT_SOURCE_DIR}/android)
# For more information, see https://doc.qt.io/qt-6/qt-add-executable.html#target-creation
else()
    if(ANDROID)
        add_library(HiveManager SHARED
            ${PROJECT_SOURCES}
            ${THIRD_PARTY_SRC}
        )
# Define properties for Android with Qt 5 after find_package() calls as:
#    set(ANDROID_PACKAGE_SOURCE_DIR "${CMAKE_CURRENT_SOURCE_DIR}/android")
    else()
        add_executable(HiveManager
            ${PROJECT_SOURCES}
            ${THIRD_PARTY_SRC}
        )
    endif()
endif()

target_link_libraries(HiveManager PRIVATE
  ${THIRD_PARTY_LIBS}
  Qt${QT_VERSION_MAJOR}::Core
  Qt${QT_VERSION_MAJOR}::Widgets
  Qt${QT_VERSION_MAJOR}::Network
)

set_target_properties(HiveManager PROPERTIES
    MACOSX_BUNDLE_GUI_IDENTIFIER my.example.com
    MACOSX_BUNDLE_BUNDLE_VERSION ${PROJECT_VERSION}
    MACOSX_BUNDLE_SHORT_VERSION_STRING ${PROJECT_VERSION_MAJOR}.${PROJECT_VERSION_MINOR}
    MACOSX_BUNDLE TRUE
    WIN32_EXECUTABLE TRUE
)

if(QT_VERSION_MAJOR EQUAL 6)
    qt_finalize_executable(HiveManager)
endif()
