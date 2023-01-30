from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from dotenv import load_dotenv, find_dotenv
from time import sleep
import requests
import os


load_dotenv(find_dotenv())
url = os.getenv("URL")
user = os.path.dirname(os.getenv("USER"))
profile = os.getenv("PROFILE")
host = os.getenv("REQUEST_HOST")

options = Options()
options.add_argument(f"--user-data-dir={user}")
options.add_argument("--profile-directory={profile}")

# Abrir no WhatsApp Web
driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), chrome_options=options)
driver.get(url)


def bot():
    try:
        # Entrar no zap do cliente
        bolinha = driver.find_element(By.CLASS_NAME, "_1pJ9J")
        bolinha = driver.find_elements(By.CLASS_NAME, "_1pJ9J")
        clica_bolinha = bolinha[-1]
        if len(clica_bolinha.text) != 0:
            acao_bolinha = ActionChains(driver)
            acao_bolinha.move_to_element_with_offset(clica_bolinha, 0, -20)
            acao_bolinha.double_click()
            acao_bolinha.perform()
            # Pegar o contato do cliente
            cliente = driver.find_element(By.XPATH, "//*[@id='main']/header/div[2]/div/div/span").get_attribute("title")
            # Pegar a mensagem do cliente
            mensagens = [msg.text for msg in driver.find_elements(By.CLASS_NAME, "_27K43")]
            mensagem = mensagens[-1].split("\n") # mensagem e horário
            print(f"{cliente} ás {mensagem[1]}: {mensagem[0]}")
            # Responder a mensagem
            campo_texto = driver.find_element(By.XPATH, "//*[@id='main']/footer/div[1]/div/span[2]/div/div[2]/div[1]/div/div[1]")
            campo_texto.click()
            resposta = requests.get(host, params={"msg": {mensagem[0]}, "cliente": {cliente}})
            bot_resposta = resposta.text
            # print(f"resposta: {bot_resposta}")

            campo_texto.send_keys(bot_resposta, Keys.ENTER)
        # Voltar para o contato padrão
        contato_padrao = driver.find_element(By.CLASS_NAME, "_1pJ9J")
        acao_contato = ActionChains(driver)
        acao_contato.move_to_element_with_offset(contato_padrao, 0, -20)
        acao_contato.click()
        acao_contato.perform()
    except:
        pass
    finally:
        print("buscando novas mensagens")
        sleep(3)


try:
    while True:
        bot()
except KeyboardInterrupt:
    pass
