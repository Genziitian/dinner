package com.dinepos.app.presentation.cashier

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.dinepos.app.DinePosApp
import com.dinepos.app.core.utils.Resource
import com.dinepos.app.domain.model.CartItem
import com.dinepos.app.domain.model.ItemVariant
import com.dinepos.app.domain.model.MenuItem
import com.dinepos.app.domain.model.Order
import com.dinepos.app.domain.repository.MenuRepository
import com.dinepos.app.domain.repository.OrderRepository
import kotlinx.coroutines.flow.MutableSharedFlow
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.SharedFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asSharedFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class CashierUiState(
    val isLoading: Boolean = false,
    val isSubmitting: Boolean = false,
    val items: List<MenuItem> = emptyList(),
    val filteredItems: List<MenuItem> = emptyList(),
    val selectedCategory: String = "all", // "all", "portion", "piece", "weight"
    val searchQuery: String = "",
    val previewOrderNumber: Int = 1,
    val cartItems: List<CartItem> = emptyList(),
    val customerName: String = "",
    val customerPhone: String = "",
    val selectedPayment: String = "Cash",
    val showCartBottomSheet: Boolean = false,
    val activeItemForModal: MenuItem? = null,
    val errorMessage: String? = null,
    val restaurantName: String = ""
)

sealed class CashierEvent {
    data class OrderSubmittedSuccess(val order: Order) : CashierEvent()
    data class ShowToast(val message: String) : CashierEvent()
}

class CashierViewModel(
    private val menuRepository: MenuRepository = DinePosApp.instance.menuRepository,
    private val orderRepository: OrderRepository = DinePosApp.instance.orderRepository
) : ViewModel() {

    private val sessionManager = DinePosApp.instance.sessionManager

    private val _uiState = MutableStateFlow(
        CashierUiState(restaurantName = sessionManager.getRestaurantName())
    )
    val uiState: StateFlow<CashierUiState> = _uiState.asStateFlow()

    private val _events = MutableSharedFlow<CashierEvent>()
    val events: SharedFlow<CashierEvent> = _events.asSharedFlow()

    init {
        loadMenu()
    }

    fun loadMenu() {
        viewModelScope.launch {
            _uiState.value = _uiState.value.copy(isLoading = true, errorMessage = null)
            when (val result = menuRepository.getMenuItems(includeInactive = false)) {
                is Resource.Success -> {
                    val (items, previewNum) = result.data
                    _uiState.value = _uiState.value.copy(
                        isLoading = false,
                        items = items,
                        previewOrderNumber = previewNum,
                        restaurantName = sessionManager.getRestaurantName()
                    )
                    applyFilter()
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

    fun onSearchQueryChange(query: String) {
        _uiState.value = _uiState.value.copy(searchQuery = query)
        applyFilter()
    }

    fun onCategorySelect(category: String) {
        _uiState.value = _uiState.value.copy(selectedCategory = category)
        applyFilter()
    }

    private fun applyFilter() {
        val state = _uiState.value
        val filtered = state.items.filter { item ->
            val matchesCat = if (state.selectedCategory == "all") true else item.itemType == state.selectedCategory
            val matchesSearch = if (state.searchQuery.isBlank()) true else item.name.contains(state.searchQuery, ignoreCase = true)
            matchesCat && matchesSearch
        }
        _uiState.value = state.copy(filteredItems = filtered)
    }

    fun onItemClicked(item: MenuItem) {
        _uiState.value = _uiState.value.copy(activeItemForModal = item)
    }

    fun dismissItemModal() {
        _uiState.value = _uiState.value.copy(activeItemForModal = null)
    }

    fun addVariantToCart(item: MenuItem, variant: ItemVariant, quantity: Double) {
        if (quantity <= 0) return

        val currentCart = _uiState.value.cartItems.toMutableList()
        val existingIndex = currentCart.indexOfFirst { it.item.id == item.id && it.variant.id == variant.id }

        if (existingIndex >= 0) {
            val existing = currentCart[existingIndex]
            currentCart[existingIndex] = existing.copy(quantity = existing.quantity + quantity)
        } else {
            currentCart.add(CartItem(item = item, variant = variant, quantity = quantity, unitPrice = variant.price))
        }

        _uiState.value = _uiState.value.copy(
            cartItems = currentCart,
            activeItemForModal = null
        )
    }

    fun updateCartItemQuantity(index: Int, delta: Double) {
        val currentCart = _uiState.value.cartItems.toMutableList()
        if (index in currentCart.indices) {
            val item = currentCart[index]
            val newQty = item.quantity + delta
            if (newQty <= 0) {
                currentCart.removeAt(index)
            } else {
                currentCart[index] = item.copy(quantity = newQty)
            }
            _uiState.value = _uiState.value.copy(cartItems = currentCart)
        }
    }

    fun removeCartItem(index: Int) {
        val currentCart = _uiState.value.cartItems.toMutableList()
        if (index in currentCart.indices) {
            currentCart.removeAt(index)
            _uiState.value = _uiState.value.copy(cartItems = currentCart)
        }
    }

    fun clearCart() {
        _uiState.value = _uiState.value.copy(cartItems = emptyList())
    }

    fun setShowCartBottomSheet(show: Boolean) {
        _uiState.value = _uiState.value.copy(showCartBottomSheet = show)
    }

    fun onCustomerNameChange(name: String) {
        _uiState.value = _uiState.value.copy(customerName = name)
    }

    fun onCustomerPhoneChange(phone: String) {
        _uiState.value = _uiState.value.copy(customerPhone = phone)
    }

    fun onPaymentMethodChange(method: String) {
        _uiState.value = _uiState.value.copy(selectedPayment = method)
    }

    fun submitOrder() {
        val state = _uiState.value
        if (state.cartItems.isEmpty()) return

        viewModelScope.launch {
            _uiState.value = state.copy(isSubmitting = true)
            when (val result = orderRepository.submitOrder(
                customerName = state.customerName,
                customerPhone = state.customerPhone,
                paymentMethod = state.selectedPayment,
                cartItems = state.cartItems
            )) {
                is Resource.Success -> {
                    _uiState.value = _uiState.value.copy(
                        isSubmitting = false,
                        cartItems = emptyList(),
                        customerName = "",
                        customerPhone = "",
                        showCartBottomSheet = false,
                        previewOrderNumber = result.data.orderNumber + 1
                    )
                    _events.emit(CashierEvent.OrderSubmittedSuccess(result.data))
                }
                is Resource.Error -> {
                    _uiState.value = _uiState.value.copy(isSubmitting = false)
                    _events.emit(CashierEvent.ShowToast(result.message))
                }
                else -> {
                    _uiState.value = _uiState.value.copy(isSubmitting = false)
                }
            }
        }
    }
}
