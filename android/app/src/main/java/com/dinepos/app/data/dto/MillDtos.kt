package com.dinepos.app.data.dto

import kotlinx.serialization.SerialName
import kotlinx.serialization.Serializable

@Serializable
data class MillServiceDto(
    val id: Int = 0,
    val name: String,
    @SerialName("name_hi") val nameHi: String? = null,
    @SerialName("rate_per_kg") val ratePerKg: Double,
    val active: Int = 1
)

@Serializable
data class MillOrderDto(
    val id: Int,
    @SerialName("order_number") val orderNumber: Int = 0,
    @SerialName("order_date") val orderDate: String = "",
    @SerialName("order_time") val orderTime: String? = null,
    @SerialName("customer_name") val customerName: String = "",
    @SerialName("customer_phone") val customerPhone: String = "",
    @SerialName("service_id") val serviceId: Int? = null,
    @SerialName("service_name") val serviceName: String = "",
    @SerialName("weight_kg") val weightKg: Double = 0.0,
    @SerialName("rate_per_kg") val ratePerKg: Double = 0.0,
    @SerialName("total_amount") val totalAmount: Double = 0.0,
    @SerialName("payment_status") val paymentStatus: String = "unpaid",
    @SerialName("payment_method") val paymentMethod: String = "cash",
    val status: String = "received",
    val notes: String? = null,
    @SerialName("created_at") val createdAt: String? = null
)

@Serializable
data class CreateMillOrderRequestDto(
    @SerialName("customer_name") val customerName: String,
    @SerialName("customer_phone") val customerPhone: String,
    @SerialName("service_id") val serviceId: Int? = null,
    @SerialName("service_name") val serviceName: String,
    @SerialName("weight_kg") val weightKg: Double,
    @SerialName("rate_per_kg") val ratePerKg: Double,
    @SerialName("payment_status") val paymentStatus: String = "unpaid",
    @SerialName("payment_method") val paymentMethod: String = "cash",
    val notes: String? = null
)

@Serializable
data class MillCustomerDto(
    val id: Int,
    val name: String,
    val phone: String,
    @SerialName("total_orders") val totalOrders: Int = 0,
    @SerialName("lifetime_amount") val lifetimeAmount: Double = 0.0,
    @SerialName("unpaid_amount") val unpaidAmount: Double = 0.0
)
