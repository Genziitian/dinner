package com.dinepos.app.data.repository

import com.dinepos.app.core.utils.Resource
import com.dinepos.app.data.api.DinePosApiService
import com.dinepos.app.data.dto.CreateOrderItemDto
import com.dinepos.app.data.dto.CreateOrderRequestDto
import com.dinepos.app.data.dto.OrderDto
import com.dinepos.app.domain.model.CartItem
import com.dinepos.app.domain.model.DailyStats
import com.dinepos.app.domain.model.Order
import com.dinepos.app.domain.model.OrderItem
import com.dinepos.app.domain.repository.OrderRepository

class OrderRepositoryImpl(private val apiService: DinePosApiService) : OrderRepository {

    override suspend fun submitOrder(
        customerName: String?,
        customerPhone: String?,
        paymentMethod: String,
        cartItems: List<CartItem>
    ): Resource<Order> {
        return try {
            val requestDto = CreateOrderRequestDto(
                customerName = customerName?.takeIf { it.isNotBlank() },
                customerPhone = customerPhone?.takeIf { it.isNotBlank() },
                paymentMethod = paymentMethod,
                items = cartItems.map { c ->
                    CreateOrderItemDto(
                        itemId = c.item.id,
                        variantId = c.variant.id,
                        quantity = c.quantity
                    )
                }
            )

            val response = apiService.createOrder(requestDto)
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                val orderDto = data?.order
                if (orderDto != null) {
                    val order = mapOrderDto(orderDto).copy(
                        receiptToken = data.receiptToken,
                        publicReceiptUrl = data.publicReceiptUrl
                    )
                    Resource.Success(order, response.body()?.message)
                } else {
                    Resource.Error("Invalid order response.")
                }
            } else {
                Resource.Error(response.body()?.message ?: "Failed to save order.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage ?: "Could not connect to server."}")
        }
    }

    override suspend fun getOrders(filter: String, search: String, paymentMethod: String): Resource<List<Order>> {
        return try {
            val response = apiService.getOrders(
                filter = filter,
                search = search.takeIf { it.isNotBlank() },
                paymentMethod = paymentMethod.takeIf { it.isNotBlank() }
            )
            if (response.isSuccessful && response.body()?.success == true) {
                val orders = response.body()?.data?.orders?.map { mapOrderDto(it) } ?: emptyList()
                Resource.Success(orders)
            } else {
                Resource.Error(response.body()?.message ?: "Failed to load orders.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun getOrderById(orderId: Int): Resource<Order> {
        return try {
            val response = apiService.getOrderById(orderId)
            if (response.isSuccessful && response.body()?.success == true) {
                val dto = response.body()?.data?.order
                if (dto != null) {
                    Resource.Success(mapOrderDto(dto))
                } else {
                    Resource.Error("Order not found.")
                }
            } else {
                Resource.Error(response.body()?.message ?: "Failed to load order.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun getReceiptByToken(token: String): Resource<Order> {
        return try {
            val response = apiService.getReceiptByToken(token)
            if (response.isSuccessful && response.body()?.success == true) {
                val data = response.body()?.data
                val dto = data?.order
                if (dto != null) {
                    val order = mapOrderDto(dto).copy(
                        receiptToken = data.receiptToken ?: token,
                        publicReceiptUrl = data.publicReceiptUrl
                    )
                    Resource.Success(order)
                } else {
                    Resource.Error("Receipt not found.")
                }
            } else {
                Resource.Error(response.body()?.message ?: "Invalid or expired receipt QR code.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    override suspend fun getCashierSummary(): Resource<DailyStats> {
        return try {
            val response = apiService.getCashierSummary()
            if (response.isSuccessful && response.body()?.success == true) {
                val statsDto = response.body()?.data?.stats
                if (statsDto != null) {
                    val stats = DailyStats(
                        totalOrders = statsDto.totalOrders,
                        totalSales = statsDto.totalSales,
                        cashSales = statsDto.cashSales,
                        onlineSales = statsDto.onlineSales,
                        avgOrderValue = statsDto.avgOrderValue
                    )
                    Resource.Success(stats)
                } else {
                    Resource.Error("No stats available.")
                }
            } else {
                Resource.Error(response.body()?.message ?: "Failed to fetch summary.")
            }
        } catch (e: Exception) {
            Resource.Error("Network error: ${e.localizedMessage}")
        }
    }

    private fun mapOrderDto(dto: OrderDto): Order {
        return Order(
            id = dto.id,
            restaurantId = dto.restaurantId,
            orderNumber = dto.orderNumber,
            orderDate = dto.orderDate,
            orderTime = dto.orderTime,
            customerName = dto.customerName,
            customerPhone = dto.customerPhone,
            subtotal = dto.subtotal,
            total = dto.total,
            paymentMethod = dto.paymentMethod,
            status = dto.status,
            receiptTokenHash = dto.receiptTokenHash,
            createdByUsername = dto.createdByUsername,
            items = dto.items.map { itemDto ->
                OrderItem(
                    id = itemDto.id,
                    orderId = itemDto.orderId,
                    itemId = itemDto.itemId,
                    itemName = itemDto.itemName,
                    variantName = itemDto.variantName,
                    quantity = itemDto.quantity,
                    unit = itemDto.unit,
                    unitPrice = itemDto.unitPrice,
                    totalPrice = itemDto.totalPrice
                )
            }
        )
    }
}
