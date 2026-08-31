package com.dinepos.app.core.utils

object Constants {
    // Default production backend URL
    const val DEFAULT_BASE_URL = "https://dinner.genziitian.in/"
    
    const val PREFS_NAME = "dinepos_secure_prefs"
    const val KEY_AUTH_TOKEN = "key_auth_token"
    const val KEY_USER_ID = "key_user_id"
    const val KEY_USERNAME = "key_username"
    const val KEY_USER_ROLE = "key_user_role"
    const val KEY_RESTAURANT_ID = "key_restaurant_id"
    const val KEY_RESTAURANT_NAME = "key_restaurant_name"
    const val KEY_RESTAURANT_ADDRESS = "key_restaurant_address"
    const val KEY_RESTAURANT_PHONE = "key_restaurant_phone"
    const val KEY_BASE_URL = "key_base_url"

    const val ROLE_SUPERADMIN = "superadmin"
    const val ROLE_MANAGER = "manager"
    const val ROLE_CASHIER = "cashier"

    const val PAYMENT_CASH = "Cash"
    const val PAYMENT_ONLINE = "Online / UPI"
}
