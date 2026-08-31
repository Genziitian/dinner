package com.dinepos.app.data.dto

import kotlinx.serialization.SerialName
import kotlinx.serialization.Serializable

@Serializable
data class AdminOverviewResponseDto(
    val restaurants: List<AdminRestaurantDto> = emptyList(),
    @SerialName("total_restaurants") val totalRestaurants: Int = 0,
    @SerialName("total_users") val totalUsers: Int = 0,
    val users: List<AdminUserDto> = emptyList()
)

@Serializable
data class AdminRestaurantDto(
    val id: Int,
    val name: String,
    val phone: String? = null,
    val address: String? = null,
    val timezone: String = "Asia/Kolkata",
    val status: String = "active",
    @SerialName("created_at") val createdAt: String? = null
)

@Serializable
data class AdminUserDto(
    val id: Int,
    val username: String,
    val role: String,
    val status: String = "active",
    @SerialName("restaurant_id") val restaurantId: Int? = null,
    @SerialName("restaurant_name") val restaurantName: String? = null
)

@Serializable
data class CreateRestaurantRequestDto(
    val name: String,
    val phone: String? = null,
    val address: String? = null,
    val timezone: String = "Asia/Kolkata",
    val status: String = "active"
)

@Serializable
data class CreateAdminUserRequestDto(
    val username: String,
    val password: String,
    val role: String,
    @SerialName("restaurant_id") val restaurantId: Int? = null,
    val status: String = "active"
)

@Serializable
data class CreateCashierRequestDto(
    val username: String,
    val password: String,
    @SerialName("confirm_password") val confirmPassword: String
)

@Serializable
data class StaffListResponseDto(
    val staff: List<AdminUserDto> = emptyList()
)

@Serializable
data class ExportDataResponseDto(
    @SerialName("restaurant_name") val restaurantName: String = "",
    @SerialName("start_date") val startDate: String = "",
    @SerialName("end_date") val endDate: String = "",
    val stats: DailyStatsDto? = null,
    val orders: List<OrderDto> = emptyList(),
    @SerialName("download_url") val downloadUrl: String = ""
)


