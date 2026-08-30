package com.dinepos.app.domain.model

data class Restaurant(
    val id: Int,
    val name: String,
    val phone: String?,
    val address: String?,
    val timezone: String = "Asia/Kolkata"
)
