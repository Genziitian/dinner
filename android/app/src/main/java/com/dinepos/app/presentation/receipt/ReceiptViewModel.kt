package com.dinepos.app.presentation.receipt

import android.graphics.Bitmap
import androidx.lifecycle.SavedStateHandle
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.Order
import com.dinepos.app.domain.repository.OrderRepository
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext

data class ReceiptUiState(
    val isLoading: Boolean = false,
    val order: Order? = null,
    val qrBitmap: Bitmap? = null,
    val errorMessage: String? = null,
    val restaurantName: String = "",
    val receiptToken: String? = null
)

class ReceiptViewModel(
    savedStateHandle: SavedStateHandle,
    private val orderRepository: OrderRepository = DinePosApp.instance.orderRepository
) : ViewModel() {

    private val orderId: Int = savedStateHandle.get<String>("orderId")?.toIntOrNull() ?: 0
    private val passedToken: String? = savedStateHandle.get<String>("token")
    private val sessionManager = DinePosApp.instance.sessionManager

    private val _uiState = MutableStateFlow(
        ReceiptUiState(
            restaurantName = sessionManager.getRestaurantName(),
            receiptToken = passedToken
        )
    )
    val uiState: StateFlow<ReceiptUiState> = _uiState.asStateFlow()

    init {
        loadOrder()
    }

    private fun loadOrder() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, errorMessage = null)
            val result = when {
                orderId > 0 -> orderRepository.getOrderById(orderId)
                !passedToken.isNullOrBlank() -> orderRepository.getReceiptByToken(passedToken)
                else -> Resource.Error("No order ID or receipt token provided.")
            }

            when (result) {
                is Resource.Success -> {
                    val order = result.data
                    val tokenToUse = passedToken ?: order.receiptToken ?: ""
                    val publicUrl = if (tokenToUse.isNotBlank()) "${sessionManager.getBaseUrl()}receipt/$tokenToUse" else ""

                    val qr = withContext(Dispatchers.Default) {
                        if (publicUrl.isNotBlank()) QrBitmapGenerator.generateQrBitmap(publicUrl, 400) else null
                    }

                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        order = order,
                        qrBitmap = qr,
                        restaurantName = sessionManager.getRestaurantName().ifBlank { "DinePOS Restaurant" }
                    )
                }
                is Resource.Error -> {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        errorMessage = result.message
                    )
                }
                else -> {
                    _uiState.value = _uiState.value.copy(isLoading = false)
                }
            }
        }
    }
}
