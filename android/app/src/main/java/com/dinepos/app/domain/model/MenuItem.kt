package com.dinepos.app.domain.model

data class MenuItem(
    val id: Int,
    val restaurantId: Int,
    val name: String,
    val itemType: String, // "piece", "weight", "portion"
    val baseUnit: String,
    val active: Boolean,
    val displayPrice: String,
    val variants: List<ItemVariant>
) {
    val isPortion: Boolean get() = itemType == "portion"
    val isPiece: Boolean get() = itemType == "piece"
    val isWeight: Boolean get() = itemType == "weight"

    val emoji: String get() = when (itemType) {
        "portion" -> "🍗"
        "piece" -> "🥚"
        "weight" -> if (baseUnit == "l" || baseUnit == "ml") "🥤" else "🌾"
        else -> "🍽️"
    }
}
