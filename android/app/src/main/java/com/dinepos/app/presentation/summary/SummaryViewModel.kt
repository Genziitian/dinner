package com.dinepos.app.presentation.summary

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.DailyStats
import com.dinepos.app.domain.repository.OrderRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class SummaryUiState(
    val isLoading: Boolean = false,
    val stats: DailyStats? = null,
    val errorMessage: String? = null,
    val restaurantName: String = ""
)

class SummaryViewModel(
    private val orderRepository: OrderRepository = DinePosApp.instance.orderRepository
) : ViewModel() {

    private val sessionManager = DinePosApp.instance.sessionManager

    private val _uiState = MutableStateFlow(
        SummaryUiState(restaurantName = sessionManager.getRestaurantName())
    )
    val uiState: StateFlow<SummaryUiState> = _uiState.asStateFlow()

    init {
        loadSummary()
    }

    fun loadSummary() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, errorMessage = null)
            when (val result = orderRepository.getCashierSummary()) {
                is Resource.Success -> {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        stats = result.data,
                        restaurantName = sessionManager.getRestaurantName()
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
}
