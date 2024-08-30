import mysql.connector

def connect():
    conn = mysql.connector.connect(
        host="localhost",
        user='root',
        password='',
        database='realhouse'
    )
    return conn
conn=connect()

# model_path = 'RFmodel2.pkl'
# if conn.is_connected():
#     # print("Connected to MySQL")
#     cursor=conn.cursor()
#     cursor.execute("SELECT * FROM modelS")
#     if cursor.fetchone():
#         cursor.close()
#         conn.close()
#         exit()


#     # save model to the database
#     cursor.execute("INSERT INTO modelS (model_data) VALUES (%s)", (model_path,))
#     conn.commit()
#     cursor.close()
#     conn.close()
#     print("MySQL connection is closed")
#     print("Model saved to the database")

# else:
#     print("Connection failed")


