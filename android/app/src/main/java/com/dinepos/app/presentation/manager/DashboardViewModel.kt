package com.dinepos.app.presentation.manager

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.DailyStats
import com.dinepos.app.domain.model.Order
import com.dinepos.app.domain.repository.ManagerRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class DashboardUiState(
    val isLoading: Boolean = false,
    val stats: DailyStats? = null,
    val recentOrders: List<Order> = emptyList(),
    val errorMessage: String? = null,
    val restaurantName: String = "",
    val isMill: Boolean = false
)

class DashboardViewModel(
    private val managerRepository: ManagerRepository = DinePosApp.instance.managerRepository
) : ViewModel() {

    private val sessionManager = DinePosApp.instance.sessionManager

    private val _uiState = MutableStateFlow(
        DashboardUiState(
            restaurantName = sessionManager.getRestaurantName(),
            isMill = sessionManager.isMill()
        )
    )
    val uiState: StateFlow<DashboardUiState> = _uiState.asStateFlow()

    init {
        loadDashboard()
    }

    fun loadDashboard() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(
                isLoading = true,
                errorMessage = null,
                isMill = sessionManager.isMill()
            )
            when (val result = managerRepository.getDashboard()) {
                is Resource.Success -> {
                    val (stats, orders) = result.data
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        stats = stats,
                        recentOrders = orders,
                        restaurantName = sessionManager.getRestaurantName(),
                        isMill = sessionManager.isMill()
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

    fun logout(onLoggedOut: () -> Unit) {
        viewModelScope.launch {
            DinePosApp.instance.authRepository.logout()
            onLoggedOut()
        }
    }
}
