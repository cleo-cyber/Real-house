from http.server import BaseHTTPRequestHandler, HTTPServer
import json
import pickle
import pandas as pd
import category_encoders as ce
import numpy as np
from model_conn import connect
import scipy.stats as stats
import datetime
conn=connect()
cursor=conn.cursor()
class ReqHandler(BaseHTTPRequestHandler):
    def _set_response(self):
        self.send_response(200)
        self.send_header('Content-type', 'application/json')
        self.send_header('Access-Control-Allow-Origin', '*')
        self.send_header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS')
        self.send_header('Access-Control-Allow-Headers', 'Content-Type')
        self.end_headers()

    def encode_data(self, input_data):
        encoder_cat1 = ce.OrdinalEncoder(cols=['title'])
        encoder_cat2 = ce.OrdinalEncoder(cols=['loc'])

        # Fit and transform the input data
        enc_data = {}
        enc_data['e_title'] = encoder_cat1.fit_transform(input_data['title']).values.reshape(-1, 1)
        enc_data['e_location'] = encoder_cat2.fit_transform(input_data['loc']).values.reshape(-1, 1)

        enc_data['bathroom'] = input_data['bathroom'].values.reshape(-1, 1)
        enc_data['bedroom'] = input_data['bedroom'].values.reshape(-1, 1)
        enc_data['parking_space'] = input_data['parking_space'].values.reshape(-1, 1)

        return enc_data

    def do_OPTIONS(self):
        self._set_response()

    def do_POST(self):
        content_length = int(self.headers['Content-Length'])
        # Read and parse the request
        post_data = self.rfile.read(content_length)
        
        # Convert the request to a dictionary
        data = json.loads(post_data)

        # Convert the dictionary to a DataFrame
        data = pd.DataFrame(data, index=[0])

        # Encode the categorical variables
        data = self.encode_data(data)
        # data.columns = ['e_title', 'e_location', 'bathroom', 'bedroom', 'parking_space']
        print(data)

        # Reshape single-dimensional arrays
        for feature, value in data.items():
            if isinstance(value, np.ndarray) and value.ndim == 1:
                data[feature] = value.reshape(-1, 1)
        

        
        # Concatenate dictionary values into a single array and reshape
        data_array = np.concatenate(list(data.values()), axis=1)
        
        # Make a prediction
        prediction = model.predict(data_array)
        all_predictions = np.array([tree.predict(data_array) for tree in model.estimators_]).flatten()
        mean_prediction = np.mean(all_predictions)
        std_prediction = np.std(all_predictions)
        confidence = 0.95
        t_critical = stats.norm.ppf((1 + confidence) / 2) 

        interval = t_critical * std_prediction
        prediction_interval = [mean_prediction - interval, mean_prediction + interval]

        input_features=post_data
        prediction_interval_min = prediction_interval[0]
        prediction_interval_max = prediction_interval[1]
        prediction_interval_min = round(prediction_interval_min, 2)
        prediction_interval_max = round(prediction_interval_max, 2)
        mean_prediction = round(mean_prediction, 2)
        prediction = round(prediction[0], 2)
        prediction_date=datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        # randomid
        prediction_id=np.random.randint(1,1000000)

        prediction_list = prediction.tolist()
        
        sql = """
        INSERT INTO model_predictions (
            prediction_id, 
            prediction_result, 
            prediction_interval_min, 
            prediction_interval_max, 
            input_features, 
            prediction_date
        ) VALUES (
            %s, %s, %s, %s, %s, %s
        );
        """
        data = (prediction_id, prediction, prediction_interval_min, prediction_interval_max, input_features, prediction_date)
        try:
            cursor.execute(sql,data)
            conn.commit()
            print("Prediction saved successfully")
        except Exception as e:
            print("Error saving prediction", e)
            conn.rollback()

        response = {'prediction': prediction_list}
        self._set_response()

        self.wfile.write(json.dumps(response).encode('utf-8'))

    def do_GET(self):
        self._set_response()
        self.wfile.write("GET request for {}".format(self.path).encode('utf-8'))

def load_model(model_path):
    with open(model_path, 'rb') as file:
        model = pickle.load(file)
    return model

# model_path = 'RFmodel2.pkl'
# model = load_model(model_path)
if conn.is_connected():
    print("Connected to MySQL")
    cursor.execute('SELECT model_data FROM models WHERE model_id = %s', (1,))
    model_data = cursor.fetchone()[0]
    if model_data:
        print("Model found")
        model = load_model(model_data)
        print("Model loaded successfully")
    else:
        print("Model not found")
else:
    print("Connection failed")


def run(server_class=HTTPServer, handler_class=ReqHandler, port=8080):
    server_address = ('', port)
    httpd = server_class(server_address, handler_class)
    print(f'Starting server on port {port}')
    print(httpd.server_address)
    httpd.serve_forever()

if __name__ == "__main__":
    run()