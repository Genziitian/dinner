package com.dinepos.app.domain.model

data class CartItem(
    val item: MenuItem,
    val variant: ItemVariant,
    val quantity: Double,
    val unitPrice: Double
) {
    val lineTotal: Double get() = quantity * unitPrice
    val variantName: String get() = variant.variantName
    val unit: String get() = variant.quantityUnit.ifBlank { item.baseUnit }
}
