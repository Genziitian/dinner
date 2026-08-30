package com.dinepos.app.domain.model

data class Order(
    val id: Int,
    val restaurantId: Int,
    val orderNumber: Int,
    val orderDate: String,
    val orderTime: String,
    val customerName: String?,
    val customerPhone: String?,
    val subtotal: Double,
    val total: Double,
    val paymentMethod: String,
    val status: String,
    val receiptTokenHash: String,
    val createdByUsername: String? = null,
    val items: List<OrderItem> = emptyList(),
    val receiptToken: String? = null,
    val publicReceiptUrl: String? = null
)
