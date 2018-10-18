'''
'参考的爬虫博客中
'http://www.cnblogs.com/linhaifeng/articles/7773496.html
'
'''
from concurrent.futures import ThreadPoolExecutor
import queue
import requests
import re
import time
import hashlib
from threading import current_thread

p=ThreadPoolExecutor(50)

def get_page(url):
	print('%s GET %s' %(current_thread().getName(),url))
	try:
		response=requests.get(url)
		if response.status_code == 200:
			return response.content
	except Exception as e:
		print(e)

def parse_index(res):
	print('%s parse index ' %current_thread().getName())
	res=res.result()
	obj=re.compile('class="items.*?<a href="(.*?)"',re.S)
	detail_urls=obj.findall(res.decode('gbk'))
	for detail_url in detail_urls:
		if not detail_url.startswith('http'):
			detail_url='http://www.xiaohuar.com'+detail_url
		p.submit(get_page,detail_url).add_done_callback(parse_detail)

def parse_detail(res):
	print('%s parse detail ' %current_thread().getName())
	res=res.result()
	obj=re.compile('id="media".*?src="(.*?)"',re.S)
	res=obj.findall(res.decode('gbk'))
	if len(res) > 0:
		movie_url=res[0]
		print('MOVIE_URL: ',movie_url)
		with open('db.txt','a') as f:
			f.write('%s\n' %movie_url)
		# save(movie_url)
		p.submit(save,movie_url)
		print('%s下载任务已经提交' %movie_url)
def save(movie_url):
	print('%s SAVE: %s' %(current_thread().getName(),movie_url))
	try:
		response=requests.get(movie_url,stream=False)
		if response.status_code == 200:
			m=hashlib.md5()
			m.update(('%s%s.mp4' %(movie_url,time.time())).encode('utf-8'))
			filename=m.hexdigest()
			with open(r'/usr/local/nginx-1.9.9/html/pythonScrapy/movies/%s.mp4' %filename,'wb') as f:
				f.write(response.content)
				f.flush()
	except Exception as e:
		print(e)

def main():
	index_url='http://www.xiaohuar.com/list-3-{0}.html'
	for i in range(5):
		p.submit(get_page,index_url.format(i,)).add_done_callback(parse_index)


if __name__ == '__main__':
	main()