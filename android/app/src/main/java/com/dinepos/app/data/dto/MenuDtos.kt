package com.dinepos.app.data.dto

import kotlinx.serialization.SerialName
import kotlinx.serialization.Serializable

@Serializable
data class MenuResponseDto(
    val items: List<MenuItemDto> = emptyList(),
    @SerialName("preview_order_number") val previewOrderNumber: Int = 1,
    @SerialName("today_date") val todayDate: String = ""
)

@Serializable
data class MenuItemDto(
    val id: Int,
    @SerialName("restaurant_id") val restaurantId: Int = 0,
    val name: String,
    @SerialName("item_type") val itemType: String = "piece",
    @SerialName("base_unit") val baseUnit: String = "piece",
    val active: Int = 1,
    @SerialName("display_price") val displayPrice: String = "",
    val variants: List<ItemVariantDto> = emptyList()
)

@Serializable
data class ItemVariantDto(
    val id: Int,
    @SerialName("item_id") val itemId: Int = 0,
    @SerialName("variant_name") val variantName: String,
    @SerialName("quantity_value") val quantityValue: Double = 1.0,
    @SerialName("quantity_unit") val quantityUnit: String = "piece",
    val price: Double = 0.0,
    val active: Int = 1
)

@Serializable
data class CreateItemRequestDto(
    val name: String,
    @SerialName("item_type") val itemType: String,
    @SerialName("base_unit") val baseUnit: String,
    val active: Int = 1,
    val variants: List<CreateVariantDto>
)

@Serializable
data class CreateVariantDto(
    @SerialName("variant_name") val variantName: String,
    @SerialName("quantity_value") val quantityValue: Double = 1.0,
    @SerialName("quantity_unit") val quantityUnit: String = "piece",
    val price: Double
)
