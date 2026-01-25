'''
	Gestion de portafolio
'''
import mysql.connector

conexion = mysql.connector.connect(
    host="localhost",
    user="admin",
    password="Portafolio2526@",
    database="portafolio"
)
cursor = conexion.cursor()

def insertar_pieza(titulo_pieza, descripcion_pieza, imagen, url, id_categoria):
	cursor.execute('''
	  INSERT INTO pieza
	  VALUES(
		NULL,
		"'''+titulo_pieza+'''",
		"'''+descripcion_pieza+'''",
		"'''+imagen.get+'''",
		"'''+url+'''"
		"'''+id_categoria+'''"
	  );
	''')
	conexion.commit()
	
def insertar_categoria(titulo_categoria,descripcion_categoria):
	cursor.execute('''
	  INSERT INTO categoria
	  VALUES(
		NULL,
		"'''+titulo_categoria+'''",
		"'''+descripcion_categoria+'''"
	  );
	''')
	
def actualizar_categoria(titulo_categoria,descripcion_categoria):
	cursor.execute('''
	  UPDATE categoria SET
		NULL,
		"'''+titulo_categoria+'''",
		"'''+descripcion_categoria+'''"
	  );
	''')
	conexion.commit()

print("########### Gestión de portafolio ##############")
print("##### v0.1 Valentin Antonio De Gennaro #########")

while True:
	print("Escoge una opción:")
	print("1.-Insertar")
	print("2.-Listar")
	print("3.-Actualizar")
	print("4.-Eliminar")
	print("5.-Salir")
	opcion = int(input("Escoge una opcion: "))
	
	if opcion == 1:
		print("1.-Insertar una categoria")
		print("2.-Insertar una pieza")
		opcion = int(input("Escoge una opcion: "))
		
		if opcion == 1:
			titulo_categoria = input("Ingresa el titulo de la categoria: ")
			descripcion_categoria = input("Introduce la descripcion de la categoria: ")
			insertar_categoria(titulo_categoria, descripcion_categoria)
			
		elif opcion == 2:
			titulo_pieza = input("Ingresa el titulo de la pieza: ")
			descripcion_pieza = input("Introduce la descripcion de la pieza: ")
			imagen = input("Introduce la imagen de la pieza: ")
			url = input("Introduce la url de la pieza: ")
			id_categoria = input("Introduce el id de la categoria: ")
			insertar_pieza(titulo_pieza, descripcion_pieza, imagen, url, id_categoria)
	
	elif opcion == 2:
		print("1.-Listar categoria")
		print("2.-Listar pieza")
		print("3.-Ambas")
		subopcion = int(input("Escoge una opcion: "))
		
		
		if subopcion == 1:
			consulta = "SELECT * FROM categoria;"
			cursor.execute(consulta)
			resultados = cursor.fetchall()
			for fila in resultados:
				print("")
				print("Identificador: ",fila[0])
				print("Titulo: ",fila[1])
				print("descripcion: ",fila[2])
				print("-" * 30)  # línea separadora entre registros
		
		
		elif subopcion == 2:
			consulta = "SELECT * FROM pieza;"
			cursor.execute(consulta)
			resultados = cursor.fetchall()
			for fila in resultados:
				print("")
				print("Identificador: ",fila[0])
				print("Titulo: ",fila[1])
				print("descripcion: ",fila[2])
				print("imagen: ",fila[3])
				print("url: ",fila[4])
				print("Id categoria: ",fila[5])
				print("-" * 30)  # línea separadora entre registros
				
		elif subopcion == 3:		
			consulta = "SELECT * FROM vista_portafolio;"
			cursor.execute(consulta)
			resultados = cursor.fetchall()
			for fila in resultados:
				print("")
				print("Titulo de la pieza: ",fila[0])
				print("Descripción de la pieza: ",fila[1])
				print("Imagen de la pieza: ",fila[2])
				print("Url de la pieza: ",fila[2])
				print("Titulo de la categoria: ",fila[2])
				print("Descripción de la categoria: ",fila[2])		
				print("-" * 30)  # línea separadora entre registros
				
				
	elif opcion == 3:
		print("1.-Modificar una categoria")
		print("2.-Modificar una pieza")
		subopcion = int(input("Escoge una opcion: "))
		
		if subopcion == 1:
			identificador = input("Introduce el Identificador a actualizar: ")
			titulo = input("Introduce el titulo de la nueva categoria: ")
			descripcion = input("Introduce la descripcion de la nueva categoria: ")
			cursor.execute('''
			  UPDATE categoria 
			  SET
			  titulo = "'''+titulo+'''",
			  descripcion = "'''+descripcion+'''",
			  WHERE Identificador = '''+identificador+'''
				''')
			conexion.commit()
		
		if subopcion == 2:
			identificador = input("Introduce el Identificador a actualizar: ")
			titulo = input("Introduce el titulo de la nueva pieza: ")
			descripcion = input("Introduce la descripcion de la nueva pieza: ")
			imagen = input("Introduce el nombre de la imagen de la nueva pieza: ")
			cursor.execute('''
			  UPDATE piezas
			  SET
			  titulo = "'''+titulo+'''",
			  descripcion = "'''+descripcion+'''",
			  imagen = "'''+imagen+'''",
			  WHERE Identificador = '''+identificador+'''
				''')
			conexion.commit()
    
	elif opcion == 4:
		print("1.-Eliminar un elemento de categoria")
		print("2.-Eliminar un elemento de pieza")
		subopcion = int(input("Escoge una opcion: "))
		
		if subopcion == 1:
			identificador = input("Introduce el Identificador a eliminar: ")
			cursor.execute("DELETE FROM categoria WHERE Identificador = "+identificador+";")
			conexion.commit()
		
		
		elif subopcion == 2:
			identificador = input("Introduce el Identificador a eliminar: ")
			cursor.execute("DELETE FROM piezas WHERE Identificador = "+identificador+";")
			conexion.commit()
	
	elif opcion == 5:
		print("Saliendo...")
		break
	
cursor.close()
conexion.close()
	
	
	
