<template>
	<!--
    SPDX-FileCopyrightText: Gesina Sands <gesina.sands@gmail.com>
    SPDX-License-Identifier: AGPL-3.0-or-later
    -->
	<div id="content" class="app-nextcloudgpt">
		<Modal v-if="showConfigModal" @close="showConfigModal = false">
			<div id="config-container">
				<h2>{{ t('nextcloudgpt', 'OpenAI Configuration') }}</h2>
				<div class="input-group">
					<label for="api-key">{{ t('nextcloudgpt', 'API Key') }}</label>
					<input id="api-key" v-model="apiKey" type="text">
				</div>
				<div class="input-group">
					<label for="model">{{ t('nextcloudgpt', 'Model') }}</label>
					<select id="model" v-model="selectedModel">
						<option value="gpt-4">
							GPT-4
						</option>
						<option value="gpt-3.5-turbo">
							GPT-3.5-turbo
						</option>
					</select>
				</div>
				<div class="input-group">
					<label for="top-p">{{ t('nextcloudgpt', 'Top P') }}</label>
					<input id="top-p"
						v-model="topP"
						type="range"
						min="0"
						max="1"
						step="0.01">
				</div>
				<div class="input-group">
					<label for="freq-penalty">{{ t('nextcloudgpt', 'Frequency Penalty') }}</label>
					<input id="freq-penalty"
						v-model="frequencyPenalty"
						type="range"
						min="0"
						max="1"
						step="0.01">
				</div>
				<div class="input-group">
					<label for="max-length">{{ t('nextcloudgpt', 'Max Length') }}</label>
					<input id="max-length"
						v-model="maxLength"
						type="range"
						min="1"
						max="2048"
						step="1">
				</div>
				<div class="input-group">
					<label for="pres-penalty">{{ t('nextcloudgpt', 'Presence Penalty') }}</label>
					<input id="pres-penalty"
						v-model="presencePenalty"
						type="range"
						min="0"
						max="1"
						step="0.01">
				</div>
				<div class="input-group">
					<label for="token-length">{{ t('nextcloudgpt', 'Token Length') }}</label>
					<input id="token-length"
						v-model="tokenLength"
						type="range"
						min="1"
						max="4096"
						step="1">
				</div>
				<form style="display: contents;" @submit.prevent="saveConfig">
					<button class="primary">
						{{ t('nextcloudgpt', 'Save') }}
					</button>
				</form>
			</div>
		</Modal>
		<Modal v-if="showPromptModal" @close="showPromptModal = false">
			<div id="config-container">
				<h2>{{ t('nextcloudgpt', 'System Prompt') }}</h2>
				<div class="input-group">
					<textarea id="system-prompt" v-model="systemPrompt" rows="5" />
				</div>
				<form style="display: contents;" @submit.prevent="saveSystemPrompt">
					<button class="primary">
						{{ t('nextcloudgpt', 'Save') }}
					</button>
				</form>
			</div>
		</Modal>
		<Modal v-if="isLoading">
			<div class="loading-indicator">
				Generating response...
			</div>
		</Modal>
		<AppNavigation>
			<ActionButton icon="icon-settings" @click="showConfigModal = true">
				{{ t('nextcloudgpt', 'API Config') }}
			</ActionButton>
			<div class="settings-display">
				<div><strong>{{ t('nextcloudgpt', 'Current Settings:') }}</strong></div>
				<div>{{ t('nextcloudgpt', 'API Key') }}: {{ apiKey ? '*****' + apiKey.slice(-5) : 'Not set' }}</div>
				<div>{{ t('nextcloudgpt', 'Model') }}: {{ selectedModel }}</div>
				<div>{{ t('nextcloudgpt', 'Top P') }}: {{ topP }}</div>
				<div>{{ t('nextcloudgpt', 'Frequency Penalty') }}: {{ frequencyPenalty }}</div>
				<div>{{ t('nextcloudgpt', 'Max Length') }}: {{ maxLength }}</div>
				<div>{{ t('nextcloudgpt', 'Presence Penalty') }}: {{ presencePenalty }}</div>
				<div>{{ t('nextcloudgpt', 'Token Length') }}: {{ tokenLength }}</div>
			</div>
			<ActionButton icon="icon-add" @click="showPromptModal = true">
				{{ t('nextcloudgpt', 'Add System Prompt') }}
			</ActionButton>
			<div><strong>{{ t('nextcloudgpt', 'Current System Prompt:') }}</strong></div>
			<div class="system-prompt-display">
				{{ systemPrompt ?? t('nextcloudgpt', 'No system prompt set') }}
			</div>
			<ActionButton icon="icon-delete" @click="deleteAllMessages">
				{{ t('nextcloudgpt', 'Delete All Messages') }}
			</ActionButton>
			<div>
				<h3>All Notes</h3>
				<ul>
					<li v-for="note in notes" :key="note.id">
						{{ note.title }}
					</li>
				</ul>
			</div>
		</AppNavigation>
		<AppContent>
			<div id="chat-container">
				<div class="messages-container">
					<div v-for="(message, index) in messages" :key="index" :class="message.role">
						{{ message.message }}
					</div>
				</div>
				<div class="input-container">
					<input ref="userInput"
						v-model="userInput"
						type="text"
						@keyup.enter="sendMessage">
					<button @click="sendMessage">
						{{ t('nextcloudgpt', 'Send') }}
					</button>
				</div>
			</div>
		</AppContent>
	</div>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import Modal from '@nextcloud/vue/dist/Components/Modal'

import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'

export default {
	name: 'App',
	components: {
		ActionButton,
		AppContent,
		AppNavigation,
		Modal,
	},
	data() {
		return {
			showConfigModal: false,
			showPromptModal: false,
			apiKey: '',
			selectedModel: 'gpt-4',
			topP: 0.9,
			frequencyPenalty: 0,
			maxLength: 2048,
			presencePenalty: 0,
			tokenLength: 4096,
			messages: [],
			userInput: '',
			isLoading: false,
			systemPrompt: null,
		}
	},
	/**
	 * Fetch list of messages when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/nextcloudgpt/messages'))
			for (let i = 0; i < response.data.length; i++) {
				this.messages.push(response.data[i])
			}
			this.loadConfig()
			this.loadSystemPrompt()
		} catch (e) {
			console.error(e)
			showError(t('nextcloudgpt', 'Could not fetch messages'))
		}
		this.loading = false
	},
	methods: {
		saveSystemPrompt() {
			try {
				axios.put(generateUrl('/apps/nextcloudgpt/system_prompts'), {
					prompt: this.systemPrompt,
				})
			} catch (error) {
				console.error(error)
			}
			this.showPromptModal = false
		},
		async loadSystemPrompt() {
			try {
				const response = await axios.get(generateUrl('/apps/nextcloudgpt/system_prompts'))
				if (response.data.length > 0) {
					const prompt = response.data[0]
					this.systemPrompt = prompt.prompt
				}
			} catch (error) {
				console.error(error)
			}
		},
		async deleteAllMessages() {
			try {
				this.isLoading = true
				const response = await axios.delete(generateUrl('/apps/nextcloudgpt/delete-all'))
				this.messages = []
				this.isLoading = false
			} catch (e) {
				console.error(e)
				showError(t('nextcloudgpt', 'Could not delete all messages'))
			}
		},
		async loadConfig() {
			try {
				const response = await axios.get(generateUrl('/apps/nextcloudgpt/openai_configs'))
				if (response.data.length > 0) {
					const config = response.data[0]
					this.apiKey = config.apiKey
					this.selectedModel = config.selectedModel
					this.topP = config.topP
					this.frequencyPenalty = config.frequencyPenalty
					this.maxLength = config.maxLength
					this.presencePenalty = config.presencePenalty
					this.tokenLength = config.tokenLength
				}
			} catch (error) {
				console.error(error)
			}
		},
		async saveMessage(message, role) {
			this.isLoading = true
			try {
				const response = await axios.post(generateUrl('/apps/nextcloudgpt/messages'), {
					message,
					role,
				})
				// assuming response.data is an array of messages
				this.messages.push(...response.data)
			} catch (e) {
				console.error(e)
				showError(t('nextcloudgpt', 'Could not save the message'))
			}
			this.isLoading = false
    	},
		async saveConfig() {
			// Save API key and model configuration here
			// localStorage.setItem('apiKey', this.apiKey)
			// Example: localStorage.setItem('selectedModel', this.selectedModel)
			// Save other settings as needed
			try {
				const config = {
					apiKey: this.apiKey,
					selectedModel: this.selectedModel,
					topP: this.topP,
					frequencyPenalty: this.frequencyPenalty,
					maxLength: this.maxLength,
					presencePenalty: this.presencePenalty,
					tokenLength: this.tokenLength,
				}
				await axios.put(generateUrl('/apps/nextcloudgpt/openai_configs'), config)
			} catch (error) {
				console.error(error)
			}
			this.showConfigModal = false
		},
		sendMessage() {
			// Send user input to backend and display response
			this.saveMessage(this.userInput, 'user')
			this.userInput = ''
			// Example: get bot response and update messages array
			// this.messages.push({ sender: 'bot', text: 'Bot response' });
		},
	},
}
</script>
<style scoped>
    #app-content > div {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        padding: 20px;
    }

    #chat-container {
        display: flex;
        flex-direction: column;
        height: 100%;
        width: 100%;
		margin: 3rem 0 3rem 0;
		border: #a4a4a4 1px solid;
    }

    .messages-container {
        flex-grow: 1;
        overflow-y: auto;
    }

    .input-container {
        display: flex;
        justify-content: space-between;
        margin: 2rem auto 3rem auto;
		width: 75%;
    }

    .user {
        align-self: flex-end;
        background-color: #e0e0e0;
        border-radius: 5px;
        margin-bottom: 5px;
        padding: 5px;
    }

    .bot {
        align-self: flex-start;
        background-color: #f0f0f0;
        border-radius: 5px;
        margin-bottom: 5px;
        padding: 5px;
    }

    input[type='text'] {
        flex-grow: 1;
    }

    button {
        margin-left: 5px;
    }

	#config-container {
    	display: flex;
    	flex-direction: column;
	}

	#config-container > h2 {
		margin-left: auto;
		margin-right: auto;
	}

	.input-group {
		display: flex;
		align-items: center;
		margin-bottom: 10px;
	}

	.input-group label {
		flex: 0 0 200px;
		text-align: left;
		margin: 0 2em 0 2em;
	}

	.input-group input[type="text"],
	.input-group select {
		flex-grow: 1;
	}

	.input-group input[type="range"] {
		flex-grow: 1;
		margin-left: 10px;
	}

	.settings-display {
		display: flex;
		flex-direction: column;
		font-size: 0.8rem;
		margin-top: 10px;
		padding: 5px;
	}

	.loading-indicator {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    }

	#system-prompt {
		width: 100%;
		margin: 1rem;
	}

	.system-prompt-display {
		max-height: 200px;
		overflow-y: auto;
		padding: 5px;
	}

</style>
