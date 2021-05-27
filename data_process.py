import csv
import os
import pandas as pd
import datetime
import time

def compare_time(time1, time2):
        s_time = time.strptime(time1,'%H:%M')
        e_time = time.strptime(time2,'%H:%M')
        return s_time > e_time


stations = pd.read_csv('./data/stationlist.csv',sep = ',',names = ['sl_station_id','sl_station_name','sl_city_name'])

f1 = open('./train-2016-10/all-stations.txt','r',encoding='utf-8')
f2 = open('./data/stationlist.csv','w',newline = '',encoding='utf-8')
csv_writer = csv.writer(f2,delimiter=',')
#csv_writer.writerow(["sl_station_id","sl_station_name","sl_city_name"])
for line in f1:
        line = line.strip('\n')
        line = line.strip(' ')
        line = line.split(',')
        csv_writer.writerow([int(line[0]),line[1],line[2]])
f1.close()
f2.close()

rootdir = './train-2016-10'
save_file = './data/passstation.csv'

f2 = open(save_file,'w',newline = '',encoding='utf-8')
csv_writer = csv.writer(f2,delimiter=',')
print('Start generate passstation data...\n')
list = os.listdir(rootdir) #列出文件夹下所有的目录与文件
for i in range(0,len(list)):
        path = os.path.join(rootdir,list[i])
        if os.path.isfile(path):
                continue
        else :
                file_list = os.listdir(path)
                for j in range(0,len(file_list)):
                        file = os.path.join(path,file_list[j])
                        f1 = pd.read_csv(file,sep=',')
                        train_id = file_list[j].strip('.csv')
                        for q in range(0,len(f1)):
                                station = f1.iloc[q]['      站名']
                                in_time = f1.iloc[q]['      到时']
                                out_time = f1.iloc[q]['      发时']
                                station = station.strip(' ')
                                in_time = in_time.strip(' ')
                                out_time = out_time.strip(' ')
                                if(in_time == '-'):
                                        in_time = ''
                                if(out_time == '-'):
                                        out_time = ''
                                j = stations.sl_station_id[stations['sl_station_name'] == station]
                                csv_writer.writerow([train_id,int(j),in_time,out_time])

save_file = './data/train.csv'
f2 = open(save_file,'w',newline = '',encoding='utf-8')
csv_writer = csv.writer(f2,delimiter=',')
print('Start generate train data...\n')
list = os.listdir(rootdir) #列出文件夹下所有的目录与文件
for i in range(0,len(list)):
        path = os.path.join(rootdir,list[i])
        if os.path.isfile(path):
                continue
        else :
                ttype = list[i]
                file_list = os.listdir(path)
                for j in range(0,len(file_list)):
                        file = os.path.join(path,file_list[j])
                        train_id = file_list[j].strip('.csv')
                        csv_writer.writerow([train_id,ttype])

save_file = './data/raw_sectioninfo.csv'
f2 = open(save_file,'w',newline = '',encoding='utf-8')
csv_writer = csv.writer(f2,delimiter=',')
print('Start generate sectioninfo data...\n')
empty_ticket_signal = ['-','0',0]
empty_time_signal = ['','-']
list = os.listdir(rootdir) #列出文件夹下所有的目录与文件
for i in range(0,len(list)):
        path = os.path.join(rootdir,list[i])
        if os.path.isfile(path):
                continue
        else :
                file_list = os.listdir(path)
                for j in range(0,len(file_list)):
                        file = os.path.join(path,file_list[j])
                        f1 = pd.read_csv(file,sep=',')
                        train_id = file_list[j].strip('.csv')
                        section_id = 1
                        sale_forbid = 0
                        for q in range(0,len(f1)-1):
                                price_Z = f1.iloc[q][' 硬座/软座']
                                price_Z = price_Z.strip(' ')
                                if(section_id == 1):
                                        price_Z = '0/0'
                                price_Z1 = price_Z.split('/',1)
                                price_Z2 = f1.iloc[q+1][' 硬座/软座']
                                price_Z2 = price_Z2.strip(' ')
                                price_Z2 = price_Z2.split('/',1)

                                price_RW = f1.iloc[q]['  软卧（上/下）']
                                price_RW = price_RW.strip(' ')
                                if(price_RW == '-' or price_RW == '-/-'):
                                        price_RW = '0/0'
                                price_RW1 = price_RW.split('/',1)
                                price_RW2 = f1.iloc[q+1]['  软卧（上/下）']
                                price_RW2 = price_RW2.strip(' ')
                                price_RW2 = price_RW2.split('/',1)

                                price_YW = f1.iloc[q]['硬卧（上/中/下）']
                                price_YW = price_YW.strip(' ')
                                if(price_YW == '-' or price_YW == '-/-/-'):
                                        price_YW = '0/0/0'
                                price_YW1 = price_YW.split('/',2)
                                price_YW2 = f1.iloc[q+1]['硬卧（上/中/下）']
                                price_YW2 = price_YW2.strip(' ')
                                price_YW2 = price_YW2.split('/',2)

                                if(price_Z2[0] in empty_ticket_signal and price_Z2[1] in empty_ticket_signal and price_RW2[0] in empty_ticket_signal and price_RW2[1] in empty_ticket_signal and price_YW2[0] in empty_ticket_signal and price_YW2[1] in empty_ticket_signal and price_YW2[2] in empty_ticket_signal):
                                        sale_forbid = 1
                                if(price_Z2[0] in empty_ticket_signal):
                                        price_YZ = 0
                                elif(price_Z1[0] in empty_ticket_signal):
                                        price_YZ = float(price_Z2[0])
                                        sale_forbid = 0
                                else:
                                        #print(price_Z2,price_Z1)
                                        price_YZ = float(price_Z2[0])
                                if(price_Z2[1] in empty_ticket_signal):
                                        price_RZ = 0
                                elif(price_Z1[1] in empty_ticket_signal):
                                        price_RZ = float(price_Z2[1])
                                        sale_forbid = 0
                                else:   
                                        price_RZ = float(price_Z2[1])
                                
                                if(price_RW2[0] in empty_ticket_signal):
                                        price_RWS = 0
                                elif(price_RW1[0] in empty_ticket_signal):
                                        price_RWS = float(price_RW2[0])
                                        sale_forbid = 0
                                else: 
                                        price_RWS = float(price_RW2[0])
                                if(price_RW2[1] in empty_ticket_signal):
                                        price_RWX = 0
                                elif(price_RW1[1] in empty_ticket_signal):
                                        price_RWX = float(price_RW2[1])
                                        sale_forbid = 0
                                else:
                                        price_RWX = float(price_RW2[1])
                                
                                if(price_YW2[0] in empty_ticket_signal):
                                        price_YWS = 0
                                elif(price_YW1[0] in empty_ticket_signal):
                                        price_YWS = float(price_YW2[0])
                                        sale_forbid = 0
                                else:
                                        price_YWS = float(price_YW2[0])
                                if(price_YW2[1] in empty_ticket_signal):
                                        price_YWZ = 0
                                elif(price_YW1[1] in empty_ticket_signal):
                                        price_YWZ = float(price_YW2[1])
                                        sale_forbid = 0
                                else:
                                        price_YWZ = float(price_YW2[1])
                                if(price_YW2[2] in empty_ticket_signal):
                                        price_YWX = 0
                                elif(price_YW1[2] in empty_ticket_signal):
                                        price_YWX = float(price_YW2[2])
                                        sale_forbid = 0
                                else:
                                        price_YWX = float(price_YW2[2])

                                start_station = f1.iloc[q]['      站名']
                                end_station = f1.iloc[q + 1]['      站名']
                                in_end_time = f1.iloc[q + 1]['      到时']
                                out_start_time = f1.iloc[q]['      发时']
                                start_station = start_station.strip(' ')
                                end_station = end_station.strip(' ')
                                in_end_time = in_end_time.strip(' ')
                                in_end_time = in_end_time.strip('-')
                                out_start_time = out_start_time.strip(' ')
                                out_start_time = out_start_time.strip('-')
                                start_station_id = stations.sl_station_id[stations['sl_station_name'] == start_station]
                                end_station_id = stations.sl_station_id[stations['sl_station_name'] == end_station]
                                duration_time_1 = f1.iloc[q]['历时（分）']
                                duration_time_2 = f1.iloc[q + 1]['历时（分）']
                                stay_time = f1.iloc[q]['停留（分）']
                                stay_time = stay_time.strip(' ')
                                stay_time = stay_time.strip('分')
                                if(section_id == 1):
                                        stay_time = 0
                                if(stay_time in empty_time_signal):
                                        stay_time = 0
                                if(section_id == 1):
                                        duration_time_1 = 0
                                if(duration_time_1 == '         -'):
                                        duration_time_1 = 0
                                if(duration_time_2 == '         -'):
                                        duration_time_2 = 0
                                duration_time = int(duration_time_2) - int(stay_time) - int(duration_time_1)
                                miles_1 = f1.iloc[q]['里程（km）']
                                miles_2 = f1.iloc[q + 1]['里程（km）']
                                if(section_id == 1):
                                        miles_1 = 0
                                if(miles_1 == '         -'):
                                        miles_1 = 0
                                if(miles_2 == '         -'):
                                        miles_2 = 0
                                mileage = int(miles_2) - int(miles_1)
                                csv_writer.writerow([train_id,section_id,int(start_station_id),int(end_station_id),out_start_time,in_end_time,sale_forbid,duration_time,mileage,price_YZ,price_RZ,price_YWS,price_YWZ,price_YWX,price_RWS,price_RWX])
                                section_id = section_id + 1

save_file = './data/sectionticket.csv'
f2 = open(save_file,'w',newline = '',encoding='utf-8')
csv_writer = csv.writer(f2,delimiter=',')
print('Start generate sectionticket data...\n')
f1 = pd.read_csv('./data/raw_sectioninfo.csv',sep =',',names = ['train_id','section_id','start_station_id','end_station_id','out_start_time','in_end_time','sale_forbid','duration_time','mileage','price_YZ','price_RZ','price_YWS','price_YWZ','price_YWX','price_RWS','price_RWX'])
train_id = ''
for q in range(0,len(f1)):
        if(f1.iloc[q]['train_id'] != train_id):
                train_id = f1.iloc[q]['train_id']
                p = q
                flag_YZ = 0
                flag_RZ = 0
                flag_YWS = 0
                flag_YWZ = 0
                flag_YWX = 0
                flag_RWS = 0
                flag_RWX = 0
                while(p < len(f1) and f1.iloc[p]['train_id'] == train_id):
                        if(f1.iloc[p]['price_YZ'] != 0):
                                flag_YZ = 1
                        if(f1.iloc[p]['price_RZ'] != 0):
                                flag_RZ = 1
                        if(f1.iloc[p]['price_YWS']!= 0):
                                flag_YWS = 1
                        if(f1.iloc[p]['price_YWZ']!= 0):
                                flag_YWZ = 1
                        if(f1.iloc[p]['price_YWX']!= 0):
                                flag_YWX = 1
                        if(f1.iloc[p]['price_RWS']!= 0):
                                flag_RWS = 1
                        if(f1.iloc[p]['price_RWX']!= 0):
                                flag_RWX = 1
                        p = p + 1

        train_id = f1.iloc[q]['train_id']

                
        section_id = f1.iloc[q]['section_id']
        if(f1.iloc[q]['price_YZ'] == 0 and flag_YZ == 0):
                ticket_YZ = 0
        else:
                ticket_YZ = 5
        if(f1.iloc[q]['price_RZ'] == 0  and flag_RZ == 0):
                ticket_RZ = 0
        else:
                ticket_RZ = 5
        if(f1.iloc[q]['price_YWS'] == 0 and flag_YWS == 0):
                ticket_YWS = 0
        else:
                ticket_YWS = 5
        if(f1.iloc[q]['price_YWZ'] == 0  and flag_YWZ == 0):
                ticket_YWZ = 0
        else:
                ticket_YWZ = 5
        if(f1.iloc[q]['price_YWX'] == 0  and flag_YWX == 0):
                ticket_YWX = 0
        else:
                ticket_YWX = 5
        if(f1.iloc[q]['price_RWS'] == 0  and flag_RWS == 0):
                ticket_RWS = 0
        else:
                ticket_RWS = 5
        if(f1.iloc[q]['price_RWX'] == 0  and flag_RWX == 0):
                ticket_RWX = 0
        else:
                ticket_RWX = 5
        d1 = datetime.date(2021,5,27);
        if(section_id == 1):
                date_flag = 0;
        elif(compare_time(str(f1.iloc[q-1]['in_end_time']),str(f1.iloc[q]['out_start_time'])) == 1):
                date_flag = date_flag + 1;
        in_flag = date_flag
        if(compare_time(str(f1.iloc[q]['out_start_time']),str(f1.iloc[q]['in_end_time'])) == 1):
                date_flag = date_flag + 1
        out_flag = date_flag
        for i in range(5):
                d2 = d1 + datetime.timedelta(days=i)
                d_in = d2 + datetime.timedelta(days=in_flag)
                d_out = d2 + datetime.timedelta(days=out_flag)
                csv_writer.writerow([train_id,section_id,d2,d_in,d_out,ticket_YZ,ticket_RZ,ticket_YWS,ticket_YWZ,ticket_YWX,ticket_RWS,ticket_RWX])

save_file = './data/sectioninfo.csv'
f2 = open(save_file,'w',newline = '',encoding='utf-8')
csv_writer = csv.writer(f2,delimiter=',')
print('Start generate sectioninfo data...\n')
empty_ticket_signal = ['-','0',0]
empty_time_signal = ['','-']
list = os.listdir(rootdir) #列出文件夹下所有的目录与文件
for i in range(0,len(list)):
        path = os.path.join(rootdir,list[i])
        if os.path.isfile(path):
                continue
        else :
                file_list = os.listdir(path)
                for j in range(0,len(file_list)):
                        file = os.path.join(path,file_list[j])
                        f1 = pd.read_csv(file,sep=',')
                        train_id = file_list[j].strip('.csv')
                        section_id = 1
                        sale_forbid = 0
                        price_YZ_save = 0
                        price_RZ_save = 0
                        price_YWS_save = 0
                        price_YWZ_save = 0
                        price_YWX_save = 0
                        price_RWS_save = 0
                        price_RWX_save = 0
                        for q in range(0,len(f1)-1):
                                price_Z = f1.iloc[q][' 硬座/软座']
                                price_Z = price_Z.strip(' ')
                                if(section_id == 1):
                                        price_Z = '0/0'
                                price_Z1 = price_Z.split('/',1)
                                price_Z2 = f1.iloc[q+1][' 硬座/软座']
                                price_Z2 = price_Z2.strip(' ')
                                price_Z2 = price_Z2.split('/',1)

                                price_RW = f1.iloc[q]['  软卧（上/下）']
                                price_RW = price_RW.strip(' ')
                                if(price_RW == '-' or price_RW == '-/-'):
                                        price_RW = '0/0'
                                price_RW1 = price_RW.split('/',1)
                                price_RW2 = f1.iloc[q+1]['  软卧（上/下）']
                                price_RW2 = price_RW2.strip(' ')
                                price_RW2 = price_RW2.split('/',1)

                                price_YW = f1.iloc[q]['硬卧（上/中/下）']
                                price_YW = price_YW.strip(' ')
                                if(price_YW == '-' or price_YW == '-/-/-'):
                                        price_YW = '0/0/0'
                                price_YW1 = price_YW.split('/',2)
                                price_YW2 = f1.iloc[q+1]['硬卧（上/中/下）']
                                price_YW2 = price_YW2.strip(' ')
                                price_YW2 = price_YW2.split('/',2)


                                if(price_Z2[0] in empty_ticket_signal and price_Z2[1] in empty_ticket_signal and price_RW2[0] in empty_ticket_signal and price_RW2[1] in empty_ticket_signal and price_YW2[0] in empty_ticket_signal and price_YW2[1] in empty_ticket_signal and price_YW2[2] in empty_ticket_signal and sale_forbid == 0):
                                        sale_forbid = 1
                                        price_YZ_save = price_Z1[0]
                                        price_RZ_save = price_Z1[1]
                                        price_YWS_save = price_YW1[0]
                                        price_YWZ_save = price_YW1[1]
                                        price_YWX_save = price_YW1[2]
                                        price_RWS_save = price_RW1[0]
                                        price_RWX_save = price_RW1[1]
                                if(price_Z2[0] in empty_ticket_signal):
                                        price_YZ = 0
                                elif(price_Z1[0] in empty_ticket_signal):
                                        price_YZ = float(price_Z2[0]) - float(price_YZ_save)
                                        sale_forbid = 0
                                else:
                                        #print(price_Z2,price_Z1)
                                        price_YZ = float(price_Z2[0]) - float(price_Z1[0])
                                if(price_Z2[1] in empty_ticket_signal):
                                        price_RZ = 0
                                elif(price_Z1[1] in empty_ticket_signal):
                                        price_RZ = float(price_Z2[1]) - float(price_RZ_save)
                                        sale_forbid = 0
                                else:   
                                        price_RZ = float(price_Z2[1]) - float(price_Z1[1])
                                
                                if(price_RW2[0] in empty_ticket_signal):
                                        price_RWS = 0
                                elif(price_RW1[0] in empty_ticket_signal):
                                        price_RWS = float(price_RW2[0]) - float(price_RWS_save)
                                        sale_forbid = 0
                                else: 
                                        price_RWS = float(price_RW2[0]) - float(price_RW1[0])
                                if(price_RW2[1] in empty_ticket_signal):
                                        price_RWX = 0
                                elif(price_RW1[1] in empty_ticket_signal):
                                        price_RWX = float(price_RW2[1]) - float(price_RWX_save)
                                        sale_forbid = 0
                                else:
                                        price_RWX = float(price_RW2[1]) - float(price_RW1[1])
                                
                                if(price_YW2[0] in empty_ticket_signal):
                                        price_YWS = 0
                                elif(price_YW1[0] in empty_ticket_signal):
                                        price_YWS = float(price_YW2[0]) - float(price_YWS_save)
                                        sale_forbid = 0
                                else:
                                        price_YWS = float(price_YW2[0]) - float(price_YW1[0])
                                if(price_YW2[1] in empty_ticket_signal):
                                        price_YWZ = 0
                                elif(price_YW1[1] in empty_ticket_signal ):
                                        price_YWZ = float(price_YW2[1]) - float(price_YWZ_save)
                                        sale_forbid = 0
                                else:
                                        price_YWZ = float(price_YW2[1]) - float(price_YW1[1])
                                if(price_YW2[2] in empty_ticket_signal):
                                        price_YWX = 0
                                elif(price_YW1[2] in empty_ticket_signal ):
                                        price_YWX = float(price_YW2[2]) - float(price_YWX_save)
                                        sale_forbid = 0
                                else:
                                        price_YWX = float(price_YW2[2]) - float(price_YW1[2])

                                start_station = f1.iloc[q]['      站名']
                                end_station = f1.iloc[q + 1]['      站名']
                                in_end_time = f1.iloc[q + 1]['      到时']
                                out_start_time = f1.iloc[q]['      发时']
                                start_station = start_station.strip(' ')
                                end_station = end_station.strip(' ')
                                in_end_time = in_end_time.strip(' ')
                                in_end_time = in_end_time.strip('-')
                                out_start_time = out_start_time.strip(' ')
                                out_start_time = out_start_time.strip('-')
                                start_station_id = stations.sl_station_id[stations['sl_station_name'] == start_station]
                                end_station_id = stations.sl_station_id[stations['sl_station_name'] == end_station]
                                duration_time_1 = f1.iloc[q]['历时（分）']
                                duration_time_2 = f1.iloc[q + 1]['历时（分）']
                                stay_time = f1.iloc[q]['停留（分）']
                                stay_time = stay_time.strip(' ')
                                stay_time = stay_time.strip('分')
                                if(section_id == 1):
                                        stay_time = 0
                                if(stay_time in empty_time_signal):
                                        stay_time = 0
                                if(section_id == 1):
                                        duration_time_1 = 0
                                if(duration_time_1 == '         -'):
                                        duration_time_1 = 0
                                if(duration_time_2 == '         -'):
                                        duration_time_2 = 0
                                duration_time = int(duration_time_2)- int(duration_time_1)
                                #if(duration_time < 0):
                                        #print(train_id)
                                stay_time = int(stay_time)
                                miles_1 = f1.iloc[q]['里程（km）']
                                miles_2 = f1.iloc[q + 1]['里程（km）']
                                if(section_id == 1):
                                        miles_1 = 0
                                if(miles_1 == '         -'):
                                        miles_1 = 0
                                if(miles_2 == '         -'):
                                        miles_2 = 0
                                mileage = int(miles_2) - int(miles_1)
                                csv_writer.writerow([train_id,section_id,int(start_station_id),int(end_station_id),stay_time,out_start_time,in_end_time,sale_forbid,duration_time,mileage,price_YZ,price_RZ,price_YWS,price_YWZ,price_YWX,price_RWS,price_RWX])
                                section_id = section_id + 1
