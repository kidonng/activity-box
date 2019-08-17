# -*- coding: utf-8 -*-
# Author: qiaoshangwuxin-5932 (https://github.com/qiaoshangwuxin-5932)

from flask import Flask,request,abort,jsonify,session,redirect,url_for
from flask_sqlalchemy import SQLAlchemy
from flask_cors import CORS
import pymysql

pymysql.install_as_MySQLdb()

app = Flask(__name__)
CORS(app, supports_credentials=True) #跨域
@app.before_request   #钩子
def app_before_request():
	print("HTTP {}  {}".format(request.method, request.url))

app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://kid:kidkidkid@198.13.42.242/data?charset=utf8' 	
# app.config['SQLALCHEMY_DATABASE_URI'] = 'mysql://ncuhome:hackweeek@123.207.25.59:3306/hackweek?charset=utf8'  #l链接数据库
app.config['SQLALCHEMY_TRACK_MODIFICATIONS'] = True
db = SQLAlchemy(app)
class Publish(db.Model): # 建立Model对象
	__tablename__ = "activity"  #  数据库对应表名
	activity_id = db.Column(db.Integer, primary_key=True,nullable=False)  #活动的ID
	username = db.Column(db.String(30), nullable=False) #发布互动的用户
	title = db.Column(db.String(30),nullable=False)  # 活动名字
	category = db.Column(db.String(30),nullable=False) #活动分类
	time = db.Column(db.DateTime)
	place = db.Column(db.String(30))  # 活动地点 
	descrption = db.Column(db.String(10000),index=True) #活动详情
	images = db.Column(db.String(100))  #图片路径
class User(db.Model):
	__tablename__ = "user"
	user_id = db.Column(db.Integer,primary_key=True,nullable=False)
	token = db.Column(db.String(255),nullable=False)	        
# 活动
@app.route('/api/activity',methods=['GET','POST','PUT'])
def activity(*args,**kwargs):
	activity_id = request.form.get("activity_id")
	username = request.form.get("username") #接受前端的user_id
	title = request.form.get("title") #接受前端的title
	category = request.form.get("category") #接受分类
	place = request.form.get("place") #接受前端的place
	time = request.form.get("time")  #接受前端提交的time
	descrption = request.form.get("descrption") #接受前端提交的descrption
	images = request.form.get("images")  #接受前端提交的图片路径
	# 发布活动
	if (request.method == 'POST'):
		activity_id = request.args.get("activity_id")
		username = request.args.get("username")
		title = request.args.get("title")
		category = request.args.get("category")
		place = request.args.get("place")
		time = request.args.get("time")
		descrption = request.args.get("descrption")
		images = request.args.get("images")
                #token = simple_cache.get(token)
		token = request.args.get("token")
		check_token = User.query.filter_by(token=token,username=username).first()#通过token进行验证
		#判断ID
		if check_token: 
			return jsonify({
				"success":'false',
				})
		activity = Publish(username=username,title=title,category=category,time=time,place=place,descrption=descrption,images=images)
		db.session.add(activity)
		db.session.commit()
		c = Publish.query.filter_by(activity_id=activity_id).first()
		return jsonify({
			"success": "true",
			"activity_id": "{}".format("activity_id"),
			})
	elif request.method == 'GET':
		activity_id = request.args.get("activity_id")
		username = request.args.get("username")
		title = request.args.get("title")
		category = request.args.get("category")
		place = request.args.get("place")
		time = request.args.get("time")
		description = request.args.get("description")
		images = request.args.get("images")
		publish = Publish.query.filter_by(activity_id=activity_id).first_or_404()  
		if not activity_id:
			abort(400)
		if not publish:
			return jsonify({
				"success":"false",
				"message":"操作失败",
				})
		list =[]
		u = publish.username
		t = publish.title
		c = publish.category
		p = publish.place
		ti = publish.time
		d = publish.descrption
		i = publish.images
		if i == null:
			list = u + t + c + p + d
		else:
			list = u + t + c + p + d + i
		return list    
		return jsonify({
			"success": "true",
			})
	elif request.method == 'PUT':
		activity_id = request.args.get("activity_id")
		username = request.args.get("username")
		title = request.args.get("title")
		category = request.args.get("category")
		place = request.args.get("place")
		time = request.args.get("time")
		description = request.args.get("description")
		images = request.args.get("images")
		# tittle = Publish.query.filter_by().first()
		#token = simple_cache.get(token)
		token = request.args.get("token")
		check_token_put = User.query.filter_by(token=token,username=username).first()
		user = Publish.query.filter_by(activity_id=activity_id).first()
		if not check_token_put:
			return jsonify({
				"success":"false",
				})
		user.title = title
		user.category = category
		user.place = place
		user.time = time    
		user.description = description
		user.images =images
		db.session.commit()
		return jsonify({
			"success":"true",
		}) 

if __name__ == '__main__':
	app.run(host="0.0.0.0",port=5000,debug=True) 				    
		# if ('activity_id','tittle','description') in session: 
		#     return jsonify({
		#         "success":"false",
		#         "message": "发布失败",
		#         }),200
		# return 'your activity have been released succesfully  '
		
			
		
		# return jsonify({
		#     "success":"true",
		#     "activity_id": "activity_id"
		#     })
		# publish = Publish.query.filter_by(activity_id=activity_id).first()
		# session['user_id'] = request.form['user_id']
		# session['activity_id'] = request.form['activity_id']
		# session['title'] = request.form['title']
		# session['place'] = request.form['place']
		# session['time'] = request.form['time'] #进行前后端赋值
		# session['description'] = request.form['description']
		# session['images'] = request.form['images']
		# return redirect(url_for('index'))
	# return '''
	#     <form action="" method="post">
	#         <p><input type=text name=username>
	#         <p><input type=submit value=Login>
	#     </form>
	# '''  
		# db.session.add(description) #添加
		# db.session.commit() #提交 
		# if publish:
		#     return jsonify({
		#     "success": true,
		#     # "message": "活动发布成功！",
		#     "data": {
		#         "activity_id":Publish.activity_id,
		#     }
		# }), 200
		# else:    
		#     return jsonify({
		#         "success":false,
		#         "message":"发布失败"
		#         })
	# 查询      
	 
			# "data": {
			#     "title": Publish.title,
			#         # "place": Publish.place,
			#         # "time": Publish.time,
			#     "description": Publish.description,
					   
		# db = pymysql.connect( "localhost",
		#               "root",
		#               "kingstom",
		#               "hackweek")   
		# cursor = db.cursor()
		# activity_id = request.args.get("activity_id")
		# search = []
		# sql = "SELECT*FROM activity_two WHERE activity_id>2; "
		# # search.append(sql)
		# # result = sql.join(search)
		
		# cursor.execute(sql)
		# result = cursor.fetchall()
		# search = result
		# cursor.close()
		# print(search)
		# return result  
	  #重新编辑                
	 

	# publish = Publish(user_id=user_id,title=title,place=place,time=time,description=description,images=images)# 把接收到的数据对到数据库内
	# db.session.add(publish) #添加
	# db.session.commit() #提交    
	#获取活动详情
# @app.route('/activity/<activity_id>',methods=["GET"])
# def get_activity():    
#     activity_id = request.args.get("activity_id")
#     if not activity_id:
#         abort(400)
#     publish = Publish.query.filter_by().first(activity_id=activity_id) #判断是否数据库内是否拥有此活动
#     if not publish:
#         return jsonify({
#             "success":false,  #没有择报错
#         }), 404
 # 重新编辑 活动   
# @app.route("/activity/remake", methods=["PUT"])
# def change_activity():
#     user_id = request.args.get("user_id")
#     activity_id = request.args.get("activity_id")
#     description = request.json.get("description")
#     publish.description = description
#     commit = db.session.commit()
#     if not commit():
#         return jsonify({
#         "message": "活动修改成功！",
#         "success": true,
#         })    
#     return jsonify({
#             "success":false,
#             "message":"活动修改失败"
#         })

# @app.route("/activity/delete",methods=["DELETE"])
# def activity_delete():
#     activity_id = request.args.get("activity_id")


