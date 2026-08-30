package com.dinepos.app.core.network

import com.dinepos.app.core.security.SessionManager
import okhttp3.Interceptor
import okhttp3.Response

class AuthInterceptor(private val sessionManager: SessionManager) : Interceptor {
    override fun intercept(chain: Interceptor.Chain): Response {
        val originalRequest = chain.request()
        val builder = originalRequest.newBuilder()
            .header("Accept", "application/json")
            .header("Content-Type", "application/json")
            .header("X-Requested-With", "XMLHttpRequest")

        val token = sessionManager.getAuthToken()
        if (!token.isNullOrBlank()) {
            builder.header("Authorization", "Bearer $token")
        }

        return chain.proceed(builder.build())
    }
}
