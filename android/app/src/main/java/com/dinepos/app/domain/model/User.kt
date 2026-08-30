package com.dinepos.app.domain.model

data class User(
    val id: Int,
    val username: String,
    val role: String,
    val restaurantId: Int?,
    val restaurantName: String? = null
) {
    val isSuperAdmin: Boolean get() = role == "superadmin"
    val isManager: Boolean get() = role == "manager" || isSuperAdmin
    val isCashier: Boolean get() = role == "cashier"
}
