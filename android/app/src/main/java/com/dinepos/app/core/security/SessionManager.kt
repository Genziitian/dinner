package com.dinepos.app.core.security

import android.content.Context
import android.content.SharedPreferences
import androidx.security.crypto.EncryptedSharedPreferences
import androidx.security.crypto.MasterKeys
import com.dinepos.app.core.utils.Constants
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow

class SessionManager(context: Context) {
    private val prefs: SharedPreferences by lazy {
        try {
            val masterKeyAlias = MasterKeys.getOrCreate(MasterKeys.AES256_GCM_SPEC)
            EncryptedSharedPreferences.create(
                Constants.PREFS_NAME,
                masterKeyAlias,
                context,
                EncryptedSharedPreferences.PrefKeyEncryptionScheme.AES256_SIV,
                EncryptedSharedPreferences.PrefValueEncryptionScheme.AES256_GCM
            )
        } catch (e: Exception) {
            // Fallback for devices/tests if encryption fails
            context.getSharedPreferences(Constants.PREFS_NAME, Context.MODE_PRIVATE)
        }
    }

    private val _isLoggedIn = MutableStateFlow(!getAuthToken().isNullOrBlank())
    val isLoggedIn: StateFlow<Boolean> = _isLoggedIn.asStateFlow()

    fun saveSession(
        token: String,
        userId: Int,
        username: String,
        role: String,
        restaurantId: Int?,
        restaurantName: String?
    ) {
        prefs.edit().apply {
            putString(Constants.KEY_AUTH_TOKEN, token)
            putInt(Constants.KEY_USER_ID, userId)
            putString(Constants.KEY_USERNAME, username)
            putString(Constants.KEY_USER_ROLE, role)
            if (restaurantId != null) putInt(Constants.KEY_RESTAURANT_ID, restaurantId) else remove(Constants.KEY_RESTAURANT_ID)
            putString(Constants.KEY_RESTAURANT_NAME, restaurantName ?: "")
            apply()
        }
        _isLoggedIn.value = true
    }

    fun clearSession() {
        prefs.edit().apply {
            remove(Constants.KEY_AUTH_TOKEN)
            remove(Constants.KEY_USER_ID)
            remove(Constants.KEY_USERNAME)
            remove(Constants.KEY_USER_ROLE)
            remove(Constants.KEY_RESTAURANT_ID)
            remove(Constants.KEY_RESTAURANT_NAME)
            apply()
        }
        _isLoggedIn.value = false
    }

    fun getAuthToken(): String? = prefs.getString(Constants.KEY_AUTH_TOKEN, null)

    fun getUserId(): Int = prefs.getInt(Constants.KEY_USER_ID, -1)

    fun getUsername(): String = prefs.getString(Constants.KEY_USERNAME, "") ?: ""

    fun getUserRole(): String = prefs.getString(Constants.KEY_USER_ROLE, "") ?: ""

    fun getRestaurantId(): Int = prefs.getInt(Constants.KEY_RESTAURANT_ID, -1)

    fun getRestaurantName(): String = prefs.getString(Constants.KEY_RESTAURANT_NAME, "") ?: ""

    fun getBaseUrl(): String = prefs.getString(Constants.KEY_BASE_URL, Constants.DEFAULT_BASE_URL) ?: Constants.DEFAULT_BASE_URL

    fun setBaseUrl(url: String) {
        val normalized = if (!url.endsWith("/")) "$url/" else url
        prefs.edit().putString(Constants.KEY_BASE_URL, normalized).apply()
    }
}
