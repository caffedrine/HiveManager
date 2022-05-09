# Variable to store thirdparty sources used by main CMake file
set(THIRD_PARTY_SRC "")
set(THIRD_PARTY_LIBS "")
set(THIRD_PARTY_INCLUDES "")

# Include spdlog logger
#add_subdirectory(${CMAKE_CURRENT_LIST_DIR}/spdlog)
#include_directories(thirdparty/spdlog/include) # there is also to build sources instead of lib
#list(APPEND THIRD_PARTY_LIBS spdlog::spdlog)

## Add hashes
list(APPEND THIRD_PARTY_INCLUDES ${CMAKE_CURRENT_LIST_DIR}/tcp_server)
file(GLOB_RECURSE lib_hashes_src ${CMAKE_CURRENT_LIST_DIR}/tcp_server/*.c ${CMAKE_CURRENT_LIST_DIR}/tcp_server/*.cpp)
#add_library(tcp_server-lib ${lib_hashes_src})
#list(APPEND THIRD_PARTY_LIBS tcp_server-lib)


#
# Resolve wildchars
#
file(GLOB_RECURSE THIRD_PARTY_SRC ${THIRD_PARTY_SRC})
include_directories(${THIRD_PARTY_INCLUDES})
