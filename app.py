# -*- coding: utf-8 -*-
from flask_restful import reqparse
from flask import Flask, render_template, jsonify
from datetime import datetime, timedelta
import time,sqlite3,json,requests
from flask_cors import CORS
from flask import request


app = Flask(__name__)
CORS(app)
METEO_API_KEY = "7070e352228b6beb3dd6e4e30da0baaa"

@app.route('/',methods=['GET'])
def home():
    ext_file = open("pageAcceuil.html", "r")
    data = ext_file.read()
    ext_file.close()
    commande=""
    commande+="/api/capteur?numTable=8&sensorid=3&amount=12&startdate=\"2020-10-20%2012:30:02\"&enddate=`\"2021-10-20%2012:30:02\""+"\n"
    return data

@app.route('/api/nbCapteur', methods=['GET'])
def nbCapteur():
    conn = sqlite3.connect('capteur.db')
    cur = conn.cursor()
    cur.execute("SELECT count(DISTINCT IdCapteur) FROM RelevesCapteurs;")
    listeTables = cur.fetchall()
    cur.close()
    conn.close()    
    return jsonify(listeTables)

@app.route('/api/listeTable', methods=['GET'])
def listeTable():
    conn = sqlite3.connect('capteur.db')
    cur = conn.cursor()
    cur.execute("SELECT name FROM sqlite_master WHERE type='table';")
    listeTables = cur.fetchall()
    print(listeTables)
    del listeTables[1]
    cur.close()
    conn.close()    
    return jsonify(listeTables)
        
@app.route('/api/capteur', methods=['GET'])
def capteur():
    try:
        numTable=int(request.args.get('numTable'))
        if(numTable==8):
            sensorid=int(request.args.get('sensorid'))
            amount=int(request.args.get('amount'))
            startdate=request.args.get('startdate')
            enddate=request.args.get('enddate')
            print("{} {} {} {}".format(sensorid,amount,startdate,enddate))
        currentTimestamp=datetime.now()                     # On récupère la date d'aujourd'hui
        date_dt=currentTimestamp.strftime("%Y-%m-%d %H:%M:%S")      #strftime permet de forcer le format de la date et donc de supprimer les décimales des secondes
        conn = sqlite3.connect('capteur.db')                # Connexion à la DB
        cur = conn.cursor()                                 # initialisation d'un curseur pour la DB
        print("Base de données correctement connectée à SQLite")

        parser = reqparse.RequestParser()                   # initialisation du parser
        parser.add_argument('time', required=False)         # ajout d'argument
        keys, values = zip(*parser.parse_args().items())    # On sépare la clé et la valeur

        if values[0] is None:                               # s'il y a aucun parametre, on prend 4 comme valeur par default
            args = 4
        elif not(float(values[0]).is_integer()):
            raise TypeError('Arguments must be integers')
        else:
            args = int(values[0])
        
        print("----------Requete de donnée pour "+str(args)+" semaines----------")
        timeLine= str((currentTimestamp-timedelta(weeks = args)).strftime("%Y-%m-%d %H:%M:%S"))     # date d'aujourd'hui - nb semaines
        
        cur.execute("SELECT name FROM sqlite_master WHERE type='table';")
        listeTables = cur.fetchall()#On recupere toute les infos des tables
        
        del listeTables[1]
        key=[]                                              # On initialise une liste vide
        dic=[]                                              # On initialise une liste vide 
        tables = [t[0] for t in listeTables]                # On garde que le nom de chaque table
        t = tables[numTable]
        condition=" "
        if numTable == 8:
            if(not(startdate is None)):
                condition+=" and DateAjout  >= "+ startdate
            if(not(enddate is None)):
                condition+=" and DateAjout  <= "+ enddate
            cur.execute("SELECT * FROM RelevesCapteurs WHERE IdCapteur  = "+str(sensorid)+ condition +"order by DateAjout;")
        elif t == "AlerteRecu":
            cur.execute("SELECT * FROM AlerteRecu WHERE DateAjout > '"+timeLine+"';")
        elif t == "Composant":
            cur.execute("SELECT * FROM Composant WHERE DateAjout > '"+timeLine+"';")
        else:
            cur.execute("SELECT * FROM "+t)         #On recupere toutes les donnees d'un table
        
        names = ([description[0] for description in cur.description])#On recupere le nom des colonnes de la table
        res=cur.fetchall()                          #On lance la requete SQL a la base de donnee
        for r in res:
            dic.append(dict(zip(names,r)))          #On passe sous forme de dictionnaire les donnees avec leurs clefs
        key.append({t:dic})                         #On incremente la liste des donnees avec le nom de leur table
        dic = []                                    #On reset la liste des donnees
    
      #  del key[1]                                      #On supprime la deuxieme case du tableau qui contient des informations sur la bdd que nous n'avons pas besoin
        #ser = json.dumps(dic) #On serialise la liste en json
        data = {'status': 'ok','data': key}
        cur.close()
        conn.close()
        print("La connexion SQLite est fermée")
        return jsonify(data)
    except sqlite3.Error as error:
        print("Erreur lors de la connexion à SQLite :", error)
    except TypeError as tp:
        print("Type error :", tp)
    return jsonify(None)


@app.route('/api/meteo/', methods=['GET'])
def meteo():
    parser = reqparse.RequestParser()                   # initialisation du parser
    parser.add_argument('place', required=False)        # ajout d'argument
    keys, values = zip(*parser.parse_args().items())    #On sépare la clé et la valeur

    if values[0] is None :
        place = "Paris"                                 #lieu par default
    else:
        place = values[0]                               # on garde la première valeur, qui correspond à la ville donnée en paramètre

    if METEO_API_KEY is None:
        # URL de test :
        METEO_API_URL = "https://samples.openweathermap.org/data/2.5/forecast?lat=0&lon=0&appid=xxx"
    else: 
        # URL avec clé :
        METEO_API_URL = "https://api.openweathermap.org/data/2.5/forecast?q="+place+"&appid=" + METEO_API_KEY

    response = requests.get(METEO_API_URL)
    content = json.loads(response.content.decode('utf-8'))

    if response.status_code != 200:
        return jsonify({
            'status': 'error',
            'message': 'La requête à l\'API météo n\'a pas fonctionné. Voici le message renvoyé par l\'API : {}'.format(content['message'])
        }), 500

    data = []                                           # On initialise une liste vide
    for prev in content["list"]:
        date = prev['dt_txt']
        temperature = prev['main']['temp'] - 273.15     # Conversion de Kelvin en °c
        temperature = round(temperature, 2)             #On arrondi la valeur à 2 chiffres après la virgule
        weather = prev['weather'][0]['main']            #On récupère le temps dans weather
        data.append([place,date, temperature, weather])
   
    return jsonify({
      'status': 'ok',
      'data': data
    })


if __name__ == "__main__":
    app.run(host='0.0.0.0', port=5000, debug=True) 