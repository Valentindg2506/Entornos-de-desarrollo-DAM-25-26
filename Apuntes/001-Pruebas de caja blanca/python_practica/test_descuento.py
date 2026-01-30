from descuento import calcular_descuento
def test_precio_negativo():
	assert calcular_descuento(-10, True) == 0
def test_cliente_vip():
	assert calcular_descuento(100, True) == 80
def test_cliente_no_vip():
	assert calcular_descuento(100, False) == 90
def test_descuento_muy_alto():
	# ESTE TEST FALLA porque la condición no se cumple
	assert calcular_descuento(600, True) == 480

