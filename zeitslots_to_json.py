import pandas as pd
dfcsv=pd.read_csv('gruppen-zeitslots-vers3.csv')
dfcsv.T.to_json('gruppen-zeitslots.json')