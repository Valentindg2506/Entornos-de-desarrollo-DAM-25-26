def calcular_descuento(precio, cliente_vip):
	if precio <= 0:
		return 0
	if cliente_vip:
		descuento = precio * 0.20
	else:
		descuento = precio * 0.10
	precio_final = precio - descuento
    # ERROR: esta condición nunca se cumple en las pruebas
	if precio_final > 500:
		precio_final -= 50
	return precio_final
	
def descuento_especial(precio):
	# FUNCIÓN QUE NO SE PRUEBA
	return precio * 0.30

