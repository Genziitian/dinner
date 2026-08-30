package com.dinepos.app.presentation.items

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.MenuItem
import com.dinepos.app.domain.repository.MenuRepository
import kotlinx.coroutines.flow.MutableSharedFlow
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.SharedFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asSharedFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class ItemsUiState(
    val isLoading: Boolean = false,
    val items: List<MenuItem> = emptyList(),
    val showAddDialog: Boolean = false,
    val errorMessage: String? = null
)

class ItemsViewModel(
    private val menuRepository: MenuRepository = DinePosApp.instance.menuRepository
) : ViewModel() {

    private val _uiState = MutableStateFlow(ItemsUiState())
    val uiState: StateFlow<ItemsUiState> = _uiState.asStateFlow()

    private val _toastEvent = MutableSharedFlow<String>()
    val toastEvent: SharedFlow<String> = _toastEvent.asSharedFlow()

    init {
        loadItems()
    }

    fun loadItems() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, errorMessage = null)
            when (val result = menuRepository.getMenuItems(includeInactive = true)) {
                is Resource.Success -> {
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        items = result.data.first
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

    fun toggleItemActive(itemId: Int) {
        viewModelScope.launch {
            when (val result = menuRepository.toggleItemActive(itemId)) {
                is Resource.Success -> {
                    _toastEvent.emit("Item status updated.")
                    loadItems()
                }
                is Resource.Error -> {
                    _toastEvent.emit(result.message)
                }
                else -> {}
            }
        }
    }

    fun setShowAddDialog(show: Boolean) {
        _uiState.value = _uiState.value.copy(showAddDialog = show)
    }

    fun createItem(name: String, itemType: String, baseUnit: String, variants: List<Map<String, Any>>) {
        viewModelScope.launch {
            when (val result = menuRepository.createItem(name, itemType, baseUnit, true, variants)) {
                is Resource.Success -> {
                    _uiState.value = _uiState.value.copy(showAddDialog = false)
                    _toastEvent.emit("Item '$name' created with ${variants.size} portion(s)!")
                    loadItems()
                }
                is Resource.Error -> {
                    _toastEvent.emit(result.message)
                }
                else -> {}
            }
        }
    }
}
