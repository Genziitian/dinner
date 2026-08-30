package com.dinepos.app.domain.repository

import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.CartItem
import com.dinepos.app.domain.model.DailyStats
import com.dinepos.app.domain.model.Order

interface OrderRepository {
    suspend fun submitOrder(
        customerName: String?,
        customerPhone: String?,
        paymentMethod: String,
        cartItems: List<CartItem>
    ): Resource<Order>

    suspend fun getOrders(filter: String = "today", search: String = "", paymentMethod: String = ""): Resource<List<Order>>
    suspend fun getOrderById(orderId: Int): Resource<Order>
    suspend fun getReceiptByToken(token: String): Resource<Order>
    suspend fun getCashierSummary(): Resource<DailyStats>
}
