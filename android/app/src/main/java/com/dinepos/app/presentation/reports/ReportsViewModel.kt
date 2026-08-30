package com.dinepos.app.presentation.reports

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.FinancialReport
import com.dinepos.app.domain.repository.ManagerRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class ReportsUiState(
    val isLoading: Boolean = false,
    val report: FinancialReport? = null,
    val selectedPeriod: String = "today",
    val errorMessage: String? = null
)

class ReportsViewModel(
    private val managerRepository: ManagerRepository = DinePosApp.instance.managerRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(ReportsUiState())
    val uiState: StateFlow<ReportsUiState> = _uiState.asStateFlow()

    init {
        loadReports()
    }

    fun loadReports() {
        val period = _uiState.value.selectedPeriod
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, errorMessage = null)
            when (val result = managerRepository.getReports(period = period)) {
                is Resource.Success -> {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        report = result.data
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

    fun onPeriodChange(period: String) {
        _uiState.value = _uiState.value.copy(selectedPeriod = period)
        loadReports()
    }
}
