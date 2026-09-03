package com.dinepos.app.data.dto

import kotlinx.serialization.SerialName
import kotlinx.serialization.Serializable

@Serializable
data class LoginRequestDto(
    val username: String,
    val password: String
)

@Serializable
data class LoginResponseDto(
    val token: String,
    val user: UserDto,
    val restaurant: RestaurantDto? = null
)

@Serializable
data class UserDto(
    val id: Int,
    val username: String,
    val role: String,
    @SerialName("restaurant_id") val restaurantId: Int? = null,
    @SerialName("restaurant_name") val restaurantName: String? = null,
    @SerialName("shop_type") val shopType: String? = null
)

@Serializable
data class RestaurantDto(
    val id: Int,
    val name: String,
    val phone: String? = null,
    val address: String? = null,
    val timezone: String = "Asia/Kolkata",
    @SerialName("shop_type") val shopType: String? = null
)
