package com.dinepos.app.presentation.orders

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.Order
import com.dinepos.app.domain.repository.OrderRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class OrdersUiState(
    val isLoading: Boolean = false,
    val orders: List<Order> = emptyList(),
    val selectedFilter: String = "today", // "today", "yesterday", "7days", "this_month", "all"
    val searchQuery: String = "",
    val paymentFilter: String = "",
    val errorMessage: String? = null
)

class OrdersViewModel(
    private val orderRepository: OrderRepository = DinePosApp.instance.orderRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(OrdersUiState())
    val uiState: StateFlow<OrdersUiState> = _uiState.asStateFlow()

    init {
        loadOrders()
    }

    fun loadOrders() {
        val state = _uiState.value
        viewModelScope.launch {
            _uiState.value = state.copy(isLoading = true, errorMessage = null)
            when (val result = orderRepository.getOrders(
                filter = state.selectedFilter,
                search = state.searchQuery,
                paymentMethod = state.paymentFilter
            )) {
                is Resource.Success -> {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        orders = result.data
                    )
                }
                is Resource.Error -> {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        errorMessage = result.message
                    )
                }
                else -> {}
            }
        }
    }

    fun onFilterChange(filter: String) {
        _uiState.value = _uiState.value.copy(selectedFilter = filter)
        loadOrders()
    }

    fun onSearchQueryChange(query: String) {
        _uiState.value = _uiState.value.copy(searchQuery = query)
        loadOrders()
    }

    fun onPaymentFilterChange(method: String) {
        _uiState.value = _uiState.value.copy(paymentFilter = method)
        loadOrders()
    }
}
