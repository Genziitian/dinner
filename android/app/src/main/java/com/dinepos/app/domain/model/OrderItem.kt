package com.dinepos.app.domain.model

data class OrderItem(
    val id: Int,
    val orderId: Int,
    val itemId: Int?,
    val itemName: String,
    val variantName: String,
    val quantity: Double,
    val unit: String,
    val unitPrice: Double,
    val totalPrice: Double
)
