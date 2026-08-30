package com.dinepos.app.data.dto

import kotlinx.serialization.SerialName
import kotlinx.serialization.Serializable

@Serializable
data class CreateOrderRequestDto(
    @SerialName("customer_name") val customerName: String? = null,
    @SerialName("customer_phone") val customerPhone: String? = null,
    @SerialName("payment_method") val paymentMethod: String = "Cash",
    val items: List<CreateOrderItemDto>
)

@Serializable
data class CreateOrderItemDto(
    @SerialName("item_id") val itemId: Int,
    @SerialName("variant_id") val variantId: Int,
    val quantity: Double
)

@Serializable
data class CreateOrderResponseDto(
    val order: OrderDto? = null,
    @SerialName("receipt_token") val receiptToken: String? = null,
    @SerialName("public_receipt_url") val publicReceiptUrl: String? = null
)

@Serializable
data class OrdersListResponseDto(
    val orders: List<OrderDto> = emptyList(),
    val filter: String = "today"
)

@Serializable
data class SingleOrderResponseDto(
    val order: OrderDto
)

@Serializable
data class OrderDto(
    val id: Int,
    @SerialName("restaurant_id") val restaurantId: Int = 0,
    @SerialName("order_number") val orderNumber: Int = 0,
    @SerialName("order_date") val orderDate: String = "",
    @SerialName("order_time") val orderTime: String = "",
    @SerialName("customer_name") val customerName: String? = null,
    @SerialName("customer_phone") val customerPhone: String? = null,
    val subtotal: Double = 0.0,
    val total: Double = 0.0,
    @SerialName("payment_method") val paymentMethod: String = "Cash",
    val status: String = "completed",
    @SerialName("receipt_token_hash") val receiptTokenHash: String = "",
    @SerialName("created_by_username") val createdByUsername: String? = null,
    val items: List<OrderItemDto> = emptyList()
)

@Serializable
data class OrderItemDto(
    val id: Int = 0,
    @SerialName("order_id") val orderId: Int = 0,
    @SerialName("item_id") val itemId: Int? = null,
    @SerialName("item_name_snapshot") val itemName: String = "",
    @SerialName("variant_name_snapshot") val variantName: String = "",
    val quantity: Double = 1.0,
    val unit: String = "piece",
    @SerialName("unit_price") val unitPrice: Double = 0.0,
    @SerialName("total_price") val totalPrice: Double = 0.0
)
