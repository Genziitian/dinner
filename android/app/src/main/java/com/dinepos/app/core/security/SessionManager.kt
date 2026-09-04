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
        restaurantName: String?,
        restaurantAddress: String? = null,
        restaurantPhone: String? = null,
        shopType: String? = "restaurant"
    ) {
        prefs.edit().apply {
            putString(Constants.KEY_AUTH_TOKEN, token)
            putInt(Constants.KEY_USER_ID, userId)
            putString(Constants.KEY_USERNAME, username)
            putString(Constants.KEY_USER_ROLE, role)
            if (restaurantId != null) putInt(Constants.KEY_RESTAURANT_ID, restaurantId) else remove(Constants.KEY_RESTAURANT_ID)
            putString(Constants.KEY_RESTAURANT_NAME, restaurantName ?: "")
            if (!restaurantAddress.isNullOrBlank()) putString(Constants.KEY_RESTAURANT_ADDRESS, restaurantAddress)
            if (!restaurantPhone.isNullOrBlank()) putString(Constants.KEY_RESTAURANT_PHONE, restaurantPhone)
            putString(Constants.KEY_SHOP_TYPE, shopType ?: "restaurant")
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
            remove(Constants.KEY_RESTAURANT_ADDRESS)
            remove(Constants.KEY_RESTAURANT_PHONE)
            remove(Constants.KEY_SHOP_TYPE)
            apply()
        }
        _isLoggedIn.value = false
    }

    fun getShopType(): String {
        val saved = prefs.getString(Constants.KEY_SHOP_TYPE, null)
        if (!saved.isNullOrBlank()) return saved
        if (getRestaurantName().contains("mill", ignoreCase = true)) {
            return "mill"
        }
        return "restaurant"
    }

    fun saveShopType(shopType: String) {
        prefs.edit().putString(Constants.KEY_SHOP_TYPE, shopType).apply()
    }

    fun isMill(): Boolean = getShopType().equals("mill", ignoreCase = true)

    fun getAuthToken(): String? = prefs.getString(Constants.KEY_AUTH_TOKEN, null)

    fun getUserId(): Int = prefs.getInt(Constants.KEY_USER_ID, -1)

    fun getUsername(): String = prefs.getString(Constants.KEY_USERNAME, "") ?: ""

    fun getUserRole(): String = prefs.getString(Constants.KEY_USER_ROLE, "") ?: ""

    fun getRestaurantId(): Int = prefs.getInt(Constants.KEY_RESTAURANT_ID, -1)

    fun getRestaurantName(): String = prefs.getString(Constants.KEY_RESTAURANT_NAME, "") ?: ""

    fun getRestaurantAddress(): String = prefs.getString(Constants.KEY_RESTAURANT_ADDRESS, "") ?: ""

    fun getRestaurantPhone(): String = prefs.getString(Constants.KEY_RESTAURANT_PHONE, "") ?: ""

    fun getBaseUrl(): String = prefs.getString(Constants.KEY_BASE_URL, Constants.DEFAULT_BASE_URL) ?: Constants.DEFAULT_BASE_URL

    fun setBaseUrl(url: String) {
        val normalized = if (!url.endsWith("/")) "$url/" else url
        prefs.edit().putString(Constants.KEY_BASE_URL, normalized).apply()
    }

    private val _language = MutableStateFlow(getLanguage())
    val language: StateFlow<String> = _language.asStateFlow()

    fun getLanguage(): String = prefs.getString("app_language", "en") ?: "en"

    fun setLanguage(lang: String) {
        prefs.edit().putString("app_language", lang).apply()
        _language.value = lang
    }

    private val _hiddenOrderIds = MutableStateFlow(getHiddenOrderIds())
    val hiddenOrderIds: StateFlow<Set<String>> = _hiddenOrderIds.asStateFlow()

    fun getHiddenOrderIds(): Set<String> = prefs.getStringSet("hidden_order_ids", emptySet()) ?: emptySet()

    fun toggleOrderHidden(orderId: Int): Boolean {
        val current = getHiddenOrderIds().toMutableSet()
        val idStr = orderId.toString()
        val isNowHidden = if (current.contains(idStr)) {
            current.remove(idStr)
            false
        } else {
            current.add(idStr)
            true
        }
        prefs.edit().putStringSet("hidden_order_ids", current).apply()
        _hiddenOrderIds.value = current
        return isNowHidden
    }

    fun isOrderHidden(orderId: Int): Boolean = getHiddenOrderIds().contains(orderId.toString())

    private val _deletedOrderIds = MutableStateFlow(getDeletedOrderIds())
    val deletedOrderIds: StateFlow<Set<String>> = _deletedOrderIds.asStateFlow()

    fun getDeletedOrderIds(): Set<String> = prefs.getStringSet("deleted_order_ids", emptySet()) ?: emptySet()

    fun markOrderDeleted(orderId: Int) {
        val current = getDeletedOrderIds().toMutableSet()
        current.add(orderId.toString())
        prefs.edit().putStringSet("deleted_order_ids", current).apply()
        _deletedOrderIds.value = current
    }

    fun isOrderDeleted(orderId: Int): Boolean = getDeletedOrderIds().contains(orderId.toString())
}
