import csv
import random
import datetime
from faker import Faker
if __name__ == '__main__':
    fake = Faker(locale='zh_CN') 
    f1 = open("./data/random_users.csv",'w',newline = '',encoding='utf-8')
    f2 = open("./data/random_orders.csv",'w',newline = '',encoding='utf-8')
    csv_writer1 = csv.writer(f1,delimiter=',')
    csv_writer2 = csv.writer(f2,delimiter=',')
    usersid = []
    usersname = []
    pnumer = []
    credit_card = []
    account_id = []
    password = []
    for i in range(0,25):
        usersid.append(fake.ssn(min_age=18, max_age=90))
        usersname.append(fake.name())
        pnumer.append(fake.phone_number())
        credit_card.append(fake.credit_card_number(card_type = 'visa16'))
        password.append(fake.password(length=10, special_chars=True, digits=True, upper_case=True, lower_case=True))
        account_id.append(fake.pystr(min_chars=None, max_chars=20) )
        csv_writer1.writerow([usersid[i],usersname[i],pnumer[i],credit_card[i],account_id[i],0,password[i]])

