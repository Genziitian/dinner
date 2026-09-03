package com.dinepos.app.domain.model

data class User(
    val id: Int,
    val username: String,
    val role: String,
    val restaurantId: Int?,
    val restaurantName: String? = null,
    val shopType: String? = "restaurant"
) {
    val isSuperAdmin: Boolean get() = role == "superadmin"
    val isManager: Boolean get() = role == "manager" || isSuperAdmin
    val isCashier: Boolean get() = role == "cashier"
    val isMill: Boolean get() = shopType?.equals("mill", ignoreCase = true) == true
}
