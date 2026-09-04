package com.dinepos.app.core.error

import retrofit2.HttpException
import java.io.IOException
import java.net.ConnectException
import java.net.SocketTimeoutException
import java.net.UnknownHostException

object ErrorClassifier {

    fun classify(throwable: Throwable?): AppErrorCode {
        if (throwable == null) return AppErrorCode.ERR_UNKNOWN

        return when (throwable) {
            is UnknownHostException -> AppErrorCode.ERR_NO_INTERNET
            is ConnectException -> AppErrorCode.ERR_SERVER_UNREACHABLE
            is SocketTimeoutException -> AppErrorCode.ERR_NETWORK_TIMEOUT
            is HttpException -> AppErrorCode.fromHttpStatus(throwable.code())
            is kotlinx.serialization.SerializationException -> AppErrorCode.ERR_DATA_PARSING
            is IOException -> {
                val msg = throwable.localizedMessage?.lowercase() ?: ""
                when {
                    msg.contains("no address") || msg.contains("network") || msg.contains("offline") -> 
                        AppErrorCode.ERR_NO_INTERNET
                    msg.contains("timeout") -> 
                        AppErrorCode.ERR_NETWORK_TIMEOUT
                    msg.contains("refused") || msg.contains("failed to connect") -> 
                        AppErrorCode.ERR_SERVER_UNREACHABLE
                    else -> AppErrorCode.ERR_NO_INTERNET
                }
            }
            else -> AppErrorCode.ERR_UNKNOWN
        }
    }

    fun classifyFromMessage(message: String?, httpCode: Int? = null): AppErrorCode {
        if (httpCode != null) {
            return AppErrorCode.fromHttpStatus(httpCode)
        }
        val lower = message?.lowercase() ?: ""
        return when {
            lower.contains("internet") || lower.contains("offline") || lower.contains("unknownhost") -> 
                AppErrorCode.ERR_NO_INTERNET
            lower.contains("timeout") -> 
                AppErrorCode.ERR_NETWORK_TIMEOUT
            lower.contains("not found") || lower.contains("404") -> 
                AppErrorCode.ERR_HTTP_NOT_FOUND
            lower.contains("unauthorized") || lower.contains("token") || lower.contains("expired") || lower.contains("401") -> 
                AppErrorCode.ERR_HTTP_UNAUTHORIZED
            lower.contains("forbidden") || lower.contains("permission") || lower.contains("403") -> 
                AppErrorCode.ERR_HTTP_FORBIDDEN
            lower.contains("server") || lower.contains("500") || lower.contains("internal") -> 
                AppErrorCode.ERR_HTTP_SERVER_ERROR
            lower.contains("parse") || lower.contains("json") || lower.contains("format") -> 
                AppErrorCode.ERR_DATA_PARSING
            else -> AppErrorCode.ERR_UNKNOWN
        }
    }
}
